<?php

namespace App\Repositories\Contracts\Auth;

interface IAuthRepository
{
    public function login(string $email, string $password);
    public function register(array $data);
    public function logout(string $token);
    public function findUserByEmail(string $email);
}
