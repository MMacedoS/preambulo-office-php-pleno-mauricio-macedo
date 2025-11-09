<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require base_path('routes/v1/auth/auth_route.php');
    require base_path('routes/v1/users/user_route.php');
    require base_path('routes/v1/movies/filme_route.php');
    require base_path('routes/v1/rental/locacao_route.php');
});
