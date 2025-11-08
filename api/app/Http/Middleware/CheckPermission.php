<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        $role = UserRole::from($user->role);

        if (!in_array($permission, $role->permissions())) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
