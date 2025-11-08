<?php

use App\Http\Controllers\users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('api.user.index');
    Route::post('/user/teste', [UserController::class, 'teste'])->name('api.user.teste');
});
