<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('rentals/info-rentals', [\App\Http\Controllers\Rental\LocacaoController::class, 'rentalActiveAndLateReturns']);
    Route::get('rentals/history', [\App\Http\Controllers\Rental\LocacaoController::class, 'rentalHistory'])->middleware('permission:ver_meus_alugueis');

    Route::apiResource(
        'rentals',
        \App\Http\Controllers\Rental\LocacaoController::class
    )
        ->middleware('permission:gerenciar_alugueis');

    Route::post(
        'rentals/{rental}/attach-movies',
        [\App\Http\Controllers\Rental\LocacaoController::class, 'attachMovies']
    )->middleware('permission:gerenciar_alugueis');

    Route::post(
        'rentals/{rental}/detach-movies',
        [\App\Http\Controllers\Rental\LocacaoController::class, 'detachMovies']
    )->middleware('permission:gerenciar_alugueis');

    Route::post(
        'rentals/{rental}/detach-movies',
        [\App\Http\Controllers\Rental\LocacaoController::class, 'detachMovies']
    )->middleware('permission:gerenciar_alugueis');

    Route::post(
        'rentals/{rental}/calculate-total',
        [\App\Http\Controllers\Rental\LocacaoController::class, 'calculateTotal']
    )->middleware('permission:gerenciar_alugueis');

    Route::post(
        'rentals/{rental}/process-return',
        [\App\Http\Controllers\Rental\LocacaoController::class, 'effectuateReturn']
    )->middleware('permission:gerenciar_alugueis');
});
