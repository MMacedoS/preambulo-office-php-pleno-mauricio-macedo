<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        $schedule->command(\App\Console\Commands\ProcessExpiredRentalsCommand::class)
            ->everyMinute()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Comando de processamento de locações expiradas executado com sucesso');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Comando de processamento de locações expiradas falhou');
            });
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('api', [
            \App\Http\Middleware\ForceJsonResponse::class
        ]);
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role'       => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
