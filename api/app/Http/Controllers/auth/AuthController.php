<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Faker\Provider\ar_EG\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $credentials['email'])->first();
        $token = $user->createToken('api_token', ['user:read', 'user:create'])->plainTextToken;

        return response()->json(['message' => 'Login successful', 'token' => $token], 200);
    }

    function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $token = $user->createToken('api_token', ['user:read', 'user:create'])->plainTextToken;

        return response()->json(['message' => 'User registered successfully', 'user' => $user, 'token' => $token], 201);
    }

    function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'No token provided'], 400);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        $accessToken->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}
