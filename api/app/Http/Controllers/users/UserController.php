<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'User index']);
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
