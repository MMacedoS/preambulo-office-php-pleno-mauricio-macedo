<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['api'])
    ->group(function () {
        require base_path('routes/v1/auth_routes.php');
        require base_path('routes/v1/candidate/candidate_routes.php');
    });
