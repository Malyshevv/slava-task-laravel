<?php

namespace App\Imports\Birthday;

use App\Events\Birthday\BirthdayEvent as BirthdayEvent;
use App\Models\Birthday\BirthdayModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class BirthdayImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
	protected array $errors = [];
	protected int $rowNumber = 1;
	protected mixed $redisKey;

	public function __construct($redisKey)
	{
		$this->redisKey = $redisKey;
	}

	public function collection(Collection $collection)
	{
		$records = [];

		foreach ($collection as $row) {
			$this->rowNumber++;
			$validator = Validator::make($row->toArray(), [
				'id' => 'required|integer|min:1|unique:birthday,external_id',
				'name' => 'required|regex:/^[a-zA-Z ]+$/',
				'date' => 'required|date_format:d.m.Y',
			]);

			if ($validator->fails()) {
				$this->errors[] = $this->rowNumber . ' - ' . implode(', ', $validator->errors()->all());
				continue;
			}

			try {
				$record = [
					'external_id' => $row['id'],
					'name' => $row['name'],
					'date' => Carbon::createFromFormat('d.m.Y', $row['date'])->format('Y-m-d'),
				];

				$records[] = $record;

				if (count($records) == 100) {
					BirthdayModel::insert($records);
				}
				$records = [];
			} catch (Exception $e) {
				$this->errors[] = $this->rowNumber . ' - ' . $e->getMessage();
			}
			//broadcast(new BirthdayEvent($records))->toOthers();
			Redis::incr($this->redisKey);
		}
	}

	public function batchSize(): int
	{
		return 1000;
	}

	public function chunkSize(): int
	{
		return 1000;
	}

	public function __destruct()
	{
		if (!empty($this->errors)) {
			$logFilePath = 'logs/errors_' . now()->format('Y_m_d_H_i_s') . '.txt';
			Storage::disk('local')->put($logFilePath, implode("\n", $this->errors));
			Log::info('Ошибка валидации, данные записаны в - ' . $logFilePath);
		}
	}
}
