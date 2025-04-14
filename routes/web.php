<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\LogController::class, 'index'])->name('dashboard');

    Route::get('/logs', [App\Http\Controllers\LogController::class, 'getLogs'])->name('logs');

    Route::post('/logs/clear', [App\Http\Controllers\LogController::class, 'clearLogs'])->name('logs.clear');

    Route::post('/api/run-job', [App\Http\Controllers\LogController::class, 'runJob'])->name('logs.run-job');

    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');



