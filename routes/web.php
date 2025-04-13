<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('logs');
});

Route::get('/logs', [App\Http\Controllers\LogController::class, 'index'])->name('logs.index');

Route::get('/api/logs', [App\Http\Controllers\LogController::class, 'logs'])->name('logs.logs');
