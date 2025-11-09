<?php

namespace Tests\Feature\Observers\Users;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Log;

class UserObserverTest extends \Tests\TestCase
{

    /**
     * Testa se o observer é acionado ao criar um usuário
     */
    public function test_user_observer_triggered_on_create(): void
    {
        // Espiar o Log para verificar se as mensagens foram registradas
        Log::spy();

        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::CLIENTE,
        ];

        // Criar usuário
        $user = User::create($userData);

        // Verificar se o usuário foi criado
        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'name' => 'João Silva',
        ]);

        // Verificar se o observer foi acionado - pelo menos uma vez
        Log::shouldHaveReceived('info')->atLeast(1);

        // Verificar se o UUID foi gerado
        $this->assertNotNull($user->uuid);
    }

    /**
     * Testa a criação de usuário através da API de registro
     */
    public function test_register_user_directly(): void
    {
        Log::spy();

        $userData = [
            'name' => 'Maria Santos',
            'email' => 'maria.santos@test.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::CLIENTE,
        ];

        $user = User::create($userData);

        // Verificar se o usuário foi criado no banco
        $this->assertDatabaseHas('users', [
            'email' => 'maria.santos@test.com',
            'name' => 'Maria Santos',
        ]);

        // Verificar logs do observer
        Log::shouldHaveReceived('info');

        $this->assertNotNull($user->uuid);
    }

    /**
     * Testa a criação de múltiplos usuários
     */
    public function test_create_multiple_users_with_observer(): void
    {
        Log::spy();

        $users = [
            ['name' => 'Usuário 1', 'email' => 'user1@example.com', 'password' => bcrypt('pass123'), 'role' => UserRole::CLIENTE],
            ['name' => 'Usuário 2', 'email' => 'user2@example.com', 'password' => bcrypt('pass123'), 'role' => UserRole::ATENDENTE],
            ['name' => 'Usuário 3', 'email' => 'user3@example.com', 'password' => bcrypt('pass123'), 'role' => UserRole::ADMINISTRADOR],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Verificar que todos os usuários foram criados
        $this->assertCount(3, User::all());

        // Verificar logs
        Log::shouldHaveReceived('info')->atLeast(6); // 2 logs por usuário (creating e created)
    }

    /**
     * Testa se o UUID é gerado corretamente
     */
    public function test_user_uuid_generation(): void
    {
        $user = User::create([
            'name' => 'Teste UUID',
            'email' => 'uuid@example.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::CLIENTE,
        ]);

        $this->assertNotNull($user->uuid);
        $this->assertNotEmpty($user->uuid);

        // Verificar que o UUID foi salvo no banco
        $savedUser = User::find($user->id);
        $this->assertEquals($user->uuid, $savedUser->uuid);
    }

    /**
     * Testa login com usuário criado
     */
    public function test_user_authentication(): void
    {
        $user = User::create([
            'name' => 'Auth Test',
            'email' => 'auth@example.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::CLIENTE,
        ]);

        // Simular que o usuário fez login
        $this->actingAs($user);

        // Verificar que estamos autenticados
        $this->assertAuthenticatedAs($user);
    }
}
