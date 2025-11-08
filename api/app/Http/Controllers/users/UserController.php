<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Users\IUsuarioRepository;
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
}
