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
            ->getJson('/api/v1/users');

        $response->assertStatus(200);
    }

    public function test_authenticated_request_with_atendente_token(): void
    {
        $auth = $this->prepareTokenAtendente();
        $token = $auth['token'];
        $user = $auth['user'];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/users');

        $response->assertStatus(200);
    }

    public function test_authenticated_request_with_cliente_token(): void
    {
        $auth = $this->prepareTokenCliente();
        $token = $auth['token'];
        $user = $auth['user'];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/users');

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
        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(401);
    }

    public function test_expired_token_access(): void
    {
        $auth = $this->prepareTokenAdmin();
        $token = $auth['token'];
        $user = $auth['user'];

        $user->tokens()->delete();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/users');

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
            ->getJson('/api/v1/users');
        $response->assertStatus(401);
    }

    public function test_user_creation_validation_errors(): void
    {
        $userData = [
            'name' => '',
            'email' => 'invalid_email',
            'password' => 'short',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_login_with_wrong_credentials(): void
    {
        $data = [
            'email' => 'invalid_email',
            'password' => 'wrong_password',
        ];

        $response = $this->postJson('/api/v1/login', $data);

        $response->assertStatus(422);
        $this->assertArrayHasKey('message', $response->json());
    }

    public function test_logout_with_invalid_token(): void
    {
        $invalidToken = 'invalid_token';

        $response = $this->withHeader('Authorization', "Bearer {$invalidToken}")
            ->postJson('/api/v1/logout');

        $response->assertStatus(422);
    }

    public function test_create_user()
    {
        $auth = $this->prepareTokenAdmin();

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201);
        $this->assertNotNull($response->json());
        $this->assertEquals('Jane Doe', $response->json('data.name'));
        $this->assertEquals('jane.doe@gmail.com', $response->json('data.email'));
        $this->assertEquals('cliente', $response->json('data.role'));
    }

    public function test_create_user_with_atendente()
    {
        $auth = $this->prepareTokenAtendente();

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201);
        $this->assertNotNull($response->json());
        $this->assertEquals('Jane Doe', $response->json('data.name'));
        $this->assertEquals('jane.doe@gmail.com', $response->json('data.email'));
        $this->assertEquals('cliente', $response->json('data.role'));
    }

    public function test_create_user_with_cliente_forbidden()
    {
        $auth = $this->prepareTokenCliente();

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(403);
        $this->assertNotNull($response->json());
        $this->assertEquals('Acesso negado', $response->json('error'));
    }

    public function test_create_user_validation_errors(): void
    {
        $auth = $this->prepareTokenAdmin();

        $userData = [
            'name' => '',
            'email' => 'asdas',
            'password' => 'short',
            'role' => '12ass',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function test_create_user_duplicate_email(): void
    {
        $auth = $this->prepareTokenAdmin();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];


        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201);
        $this->assertNotNull($response->json());
        $this->assertEquals('John Doe', $response->json('data.name'));
        $this->assertEquals('john.doe@gmail.com', $response->json('data.email'));
        $this->assertEquals('cliente', $response->json('data.role'));

        $responseDuplicate = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);
        $responseDuplicate->assertStatus(422);
        $responseDuplicate->assertJsonValidationErrors(['email']);
    }

    public function test_update_user()
    {
        $auth = $this->prepareTokenAdmin();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201);
        $this->assertNotNull($response->json());
        $this->assertEquals('John Doe', $response->json('data.name'));
        $this->assertEquals('john.doe@gmail.com', $response->json('data.email'));
        $this->assertEquals('cliente', $response->json('data.role'));
        $userId = $response->json('data.id');

        $updateData = [
            'name' => 'John Updated',
            'email' => 'john.updated@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->putJson("/api/v1/users/{$userId}", $updateData);

        $response->assertStatus(200);
        $this->assertNotNull($response->json());
        $this->assertEquals('John Updated', $response->json('data.name'));
        $this->assertEquals('john.updated@gmail.com', $response->json('data.email'));
        $this->assertEquals('cliente', $response->json('data.role'));
    }

    public function test_update_user_not_found()
    {
        $auth = $this->prepareTokenAdmin();

        $updateData = [
            'name' => 'John Updated',
            'email' => 'john.updated@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->putJson("/api/v1/users/999", $updateData);

        $response->assertStatus(422);
        $this->assertNotNull($response->json());
        $this->assertEquals('Usuário não encontrado', $response->json('message'));
    }

    public function test_delete_user()
    {
        $auth = $this->prepareTokenAdmin();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201);
        $this->assertNotNull($response->json());
        $this->assertEquals('John Doe', $response->json('data.name'));
        $this->assertEquals('john.doe@gmail.com', $response->json('data.email'));
        $this->assertEquals('cliente', $response->json('data.role'));
        $userId = $response->json('data.id');

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->deleteJson("/api/v1/users/{$userId}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['uuid' => $userId]);
    }

    public function test_delete_user_not_found()
    {
        $auth = $this->prepareTokenAdmin();

        $response = $this->withHeaders($this->getAuthHeaders($auth))
            ->deleteJson("/api/v1/users/999");

        $response->assertStatus(422);
        $this->assertNotNull($response->json());
        $this->assertEquals('Usuário não encontrado', $response->json('message'));
    }
}
