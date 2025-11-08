<?php

use App\Http\Controllers\users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/teste', [UserController::class, 'index']);

Route::post('/teste-validation', [UserController::class, 'teste']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
