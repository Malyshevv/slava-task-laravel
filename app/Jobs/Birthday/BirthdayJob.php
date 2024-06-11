<?php

namespace App\Jobs\Birthday;

use App\Imports\Birthday\BirthdayImport as BirthdayImport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redis;

class BirthdayJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

	protected $filePath;
	protected $redisKey;

	/**
	 * Create a new job instance.
	 */
	public function __construct($filePath)
	{
		$this->filePath = $filePath;
		$this->redisKey = 'birthday_import_progress_' . now()->timestamp;

		// Инициализация ключа в Redis
		Redis::set($this->redisKey, 0);
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		Excel::import(new BirthdayImport($this->redisKey), $this->filePath);
	}

	public function getRedisKey(): string
	{
		return $this->redisKey;
	}
}
