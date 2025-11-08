<?php

namespace App\Repositories\Entities\Auth;

use App\Models\User;
use App\Repositories\Contracts\Auth\IAuthRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthRepository implements IAuthRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function login(string $email, string $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = $this->findUserByEmail($email);

        if ($user) {
            $token = $user->createToken('api_token', ['user:read', 'user:create'])->plainTextToken;
            return [
                'user' => $user,
                'token' => $token,
            ];
        }

        return null;
    }

    public function register(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        $user = $this->model->create($data);
        $this->setCachedObject($user, 3600);

        $token = $user->createToken('api_token', ['user:read', 'user:create'])->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(string $token)
    {
        if (!$token) {
            return false;
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return false;
        }

        $accessToken->delete();
        return true;
    }

    public function findUserByEmail(string $email)
    {
        return $this->getFromCacheOrFetch(
            'user_email_' . $email,
            fn() => $this->model->where('email', $email)->first(),
            3600
        );
    }
}
