<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response("Api V1 running", 200);
});
