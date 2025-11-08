<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('user', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
                ->response(function ($request, array $headers) {
                    return response()->json([
                        'message' => 'Execedeu o limite de requisições, aguarde um momento e tente novamente.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip())
                ->response(function ($request, array $headers) {
                    return response()->json([
                        'message' => 'Muitas tentativas de login. Por favor, tente novamente mais tarde.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });
    }
}
