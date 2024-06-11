<?php

use App\Events\Birthday\BirthdayEvent as BirthdayEvent;
use App\Http\Controllers\Birthday\BirthdayController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
	Route::get('/', function () { return Inertia::render('Dashboard'); });
	Route::get('/dashboard', function () { return Inertia::render('Dashboard');})->name('dashboard');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

	Route::get('birthdays', [BirthdayController::class, 'index'])->name('birthday');
	Route::get('birthday/upload', [BirthdayController::class, 'upload'])->name('birthday.upload');
	Route::post('birthday/upload', [BirthdayController::class, 'uploadFile'])->name('birthday.upload.file');

	Route::get('test', function () {
		broadcast(new BirthdayEvent(['id' => 1, 'name'=> 'test', 'date' => date('y.m.d'), 'external_id' => 1]))->toOthers();
		return response()->json(['result' => true], 200);
	});

	Route::get('progress/{key}', function ($key) { return response()->json(['progress' => Redis::get($key)]);  });

});

require __DIR__.'/auth.php';
