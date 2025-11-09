<?php

namespace Tests\Feature\Controllers\Users;

class UserControllerTest extends \Tests\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_user_creation(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@gmail.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john@gmail.com']);
    }

    public function test_authenticated_request_with_admin_token(): void
    {
        $auth = $this->prepareTokenAdmin();
        $token = $auth['token'];
        $user = $auth['user'];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $response->assertStatus(200);
    }

    public function test_authenticated_request_with_atendente_token(): void
    {
        $auth = $this->prepareTokenAtendente();
        $token = $auth['token'];
        $user = $auth['user'];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $response->assertStatus(403);
    }

    public function test_authenticated_request_with_cliente_token(): void
    {
        $auth = $this->prepareTokenCliente();
        $token = $auth['token'];
        $user = $auth['user'];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $response->assertStatus(403);
    }

    public function test_token_is_valid(): void
    {
        $auth = $this->prepareTokenAdmin();
        $token = $auth['token'];
        $user = $auth['user'];

        $this->assertNotNull($token);

        $this->assertTrue($user->tokens()->count() > 0);
    }

    public function test_logout(): void
    {
        $auth = $this->prepareTokenAdmin();
        $token = $auth['token'];
        $user = $auth['user'];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $response->assertStatus(200);

        $this->assertTrue($user->tokens()->count() === 0);
    }

    public function test_access_without_token(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(401);
    }

    public function test_expired_token_access(): void
    {
        $auth = $this->prepareTokenAdmin();
        $token = $auth['token'];
        $user = $auth['user'];

        $user->tokens()->delete();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user');

        $response->assertStatus(401);
    }

    public function test_login()
    {
        $this->test_user_creation();

        $data = [
            'email' => 'john@gmail.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/v1/login', $data);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('token'));
    }

    public function test_invalid_token_access(): void
    {
        $invalidToken = 'invalid_token';

        $response = $this->withHeader('Authorization', "Bearer {$invalidToken}")
            ->getJson('/api/v1/user');
        $response->assertStatus(401);
    }
}
