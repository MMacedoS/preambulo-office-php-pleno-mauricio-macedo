<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\Movies\Filme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function mockUsuarioAdmin()
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMINISTRADOR,
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function mockUsuarioAtendente()
    {
        $user = User::factory()->create([
            'name' => 'Atendente User',
            'email' => 'atendente@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ATENDENTE,
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function mockUsuarioCliente()
    {
        $user = User::factory()->create([
            'name' => 'Cliente User',
            'email' => 'cliente@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::CLIENTE,
        ]);
        $this->actingAs($user);
        return $user;
    }

    public function prepareTokenAdmin()
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMINISTRADOR,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function prepareTokenAtendente()
    {
        $user = User::factory()->create([
            'name' => 'Atendente User',
            'email' => 'atendente@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ATENDENTE,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function prepareTokenCliente()
    {
        $user = User::factory()->create([
            'name' => 'Cliente User',
            'email' => 'cliente@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::CLIENTE,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function mockFilme()
    {
        $filmeData = Filme::factory()->make();
        return $filmeData;
    }
}
