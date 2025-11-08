<?php

use App\Http\Controllers\users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('api.user.index')->middleware('role:administrador');
});
