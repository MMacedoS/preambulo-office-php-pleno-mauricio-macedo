<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IUsuarioRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected IUsuarioRepository $usuarioRepository;

    public function __construct(IUsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function index(Request $request)
    {
        $users = $this->usuarioRepository->findAll();
        return response()->json(['data' => $users], 200);
    }

    public function teste(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if (!$validated) {
            return response()->json(['message' => 'Validation failed'], 422);
        }

        return response()->json(['status' => 'success', 'data' => $validated], 200);
    }
}
