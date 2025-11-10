<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Auth\IAuthRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected IAuthRepository $authRepository;

    public function __construct(IAuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8',
        ]);

        $result = $this->authRepository->login($credentials['email'], $credentials['password']);

        if (!$result) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        return response()->json(
            [
                'message' => 'Login bem-sucedido',
                'user' => $result['user'],
                'token' => $result['token']
            ],
            200
        );
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $result = $this->authRepository->register($validated);

        return response()->json(['message' => 'Usuário registrado com sucesso', 'user' => $result['user'], 'token' => $result['token']], 201);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Nenhum token fornecido'], 400);
        }

        $success = $this->authRepository->logout($token);

        if (!$success) {
            return response()->json(['message' => 'Token inválido'], 422);
        }

        return response()->json(['message' => 'Logout bem-sucedido']);
    }
}
