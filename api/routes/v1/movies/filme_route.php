<?php

use App\Http\Controllers\movies\FilmeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/movies', [FilmeController::class, 'index'])->name('api.movies.index')
        ->middleware('permission:ver_filmes');
    Route::post('/movies', [FilmeController::class, 'store'])->name('api.movies.store')
        ->middleware('permission:gerenciar_filmes');
    Route::put('/movies/{id}', [FilmeController::class, 'update'])->name('api.movies.update')
        ->middleware('permission:gerenciar_filmes');

    Route::delete('/movies/{id}', [FilmeController::class, 'destroy'])->name('api.movies.destroy')
        ->middleware('permission:gerenciar_filmes');
});
