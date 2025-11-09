<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\LoadEntityTrait;
use App\Repositories\Traits\FilterSearchTrait;
use App\Traits\PaginationTrait;

abstract class Controller
{
    use PaginationTrait,
        FilterSearchTrait,
        LoadEntityTrait;
}
