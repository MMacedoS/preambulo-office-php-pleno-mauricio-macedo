<?php

use App\Http\Controllers\users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('api.user.index')->middleware('permission:ver_usuarios');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('api.user.show')->middleware('permission:ver_usuarios');
    Route::post('/users', [UserController::class, 'store'])->name('api.user.store')->middleware('permission:criar_usuarios');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('api.user.update')->middleware('permission:editar_usuarios');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.user.destroy')->middleware('permission:deletar_usuarios');
});
