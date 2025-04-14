<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\LogController::class, 'index'])->name('logs.index');

Route::post('/api/run-job', [App\Http\Controllers\LogController::class, 'runJob'])->name('logs.run-job');
