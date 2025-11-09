<?php

use App\Http\Controllers\auth\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login'])->name('api.login')->middleware('throttle:login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
