<?php

namespace App\Providers;

use App\Models\Movies\Filme;
use App\Models\Rental\Locacao;
use App\Models\Rental\LocacaoFilme;
use App\Models\User;
use App\Observers\Movies\FilmeObserver;
use App\Observers\Rental\LocacaoFilmeObserver;
use App\Observers\Rental\LocacaoObserver;
use App\Observers\Users\UserObserver;
use App\Repositories\Contracts\Auth\IAuthRepository;
use App\Repositories\Contracts\Movies\IFilmeRepository;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Entities\Auth\AuthRepository;
use App\Repositories\Entities\Movies\FilmeRepository;
use App\Repositories\Entities\Users\UsuarioRepository;
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
        $this->app->bind(IUsuarioRepository::class, UsuarioRepository::class);
        $this->app->bind(IFilmeRepository::class, FilmeRepository::class);
        $this->app->bind(IAuthRepository::class, AuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filme::observe(FilmeObserver::class);
        User::observe(UserObserver::class);
        LocacaoFilme::observe(LocacaoFilmeObserver::class);
        Locacao::observe(LocacaoObserver::class);

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
