<?php

namespace App\Http\Controllers\Birthday;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UploadController;
use App\Jobs\Birthday\BirthdayJob;
use App\Models\Birthday\BirthdayModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class BirthdayController extends Controller
{
	protected UploadController $uploadController;

	public function __construct(UploadController $uploadController)
	{
		$this->uploadController = $uploadController;
	}

	public function index()
	{
		$birthdays = BirthdayModel::orderBy('date', 'desc')->paginate(10);
		$groupedBirthdays = $birthdays->groupBy('date');
		$result = $groupedBirthdays->toArray();

		$birthdays->setCollection($groupedBirthdays->flatten());
		$links = $birthdays->toArray()['links'];

		return Inertia::render('Birthday/BirthDayList', [
			'result' => $result,
			'links' => $links
		]);
	}

	public function upload()
	{
		return Inertia::render('Birthday/BirthDayUpload');
	}

	/**
	 * @throws Throwable
	 */
	public function uploadFile(Request $request)
	{
		$validateRules = [
			'file' => 'required|file|mimes:xlsx',
		];
		return $this->uploadController->upload($request, BirthdayJob::class, $validateRules);
	}
}
