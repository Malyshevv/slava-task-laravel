<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UploadController extends Controller
{
	/**
	 * @throws Throwable
	 */
	public function upload(Request $request, $jobClass, $validateRules)
	{
		$validator = Validator::make($request->all(), $validateRules);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 400);
		}

		$file = $request->file('file');
		$filePath = $file->store('uploads');
		$job = new $jobClass($filePath);

		$batch = Bus::batch([
			$job
		])->name('uploads')->dispatch();

		$redisKey = $job->getRedisKey();
		return response()->json([
			'message' => 'Файл поставлен в очередь, ожидайте!',
			'batch_id' => $batch->id,
			'link_status' => ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .'/progress/'. $redisKey,
			'redis_key' => $redisKey,
		], 200);
	}
}
