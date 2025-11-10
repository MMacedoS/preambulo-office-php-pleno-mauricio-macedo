<?php

namespace Tests\Feature\Repositories\Users;


class UserRepositoryTest extends \Tests\TestCase
{
    protected $usuarioRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->usuarioRepository = app(\App\Repositories\Entities\Users\UsuarioRepository::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_user_creation(): void
    {
        $user = $this->mockUsuarioAdmin();

        $this->assertEquals('Admin User', $user->name);
        $this->assertEquals('admin@localfilmes.com', $user->email);
        $this->assertEquals('administrador', $user->role->value);
    }

    public function test_user_role_check(): void
    {
        $adminUser = $this->mockUsuarioAdmin();
        $this->assertEquals('administrador', $adminUser->role->value);

        $atendenteUser = $this->mockUsuarioAtendente();
        $this->assertEquals('atendente', $atendenteUser->role->value);

        $clienteUser = $this->mockUsuarioCliente();
        $this->assertEquals('cliente', $clienteUser->role->value);
    }

    public function test_user_repository_instance(): void
    {
        $this->assertInstanceOf(
            \App\Repositories\Entities\Users\UsuarioRepository::class,
            $this->usuarioRepository
        );

        $this->assertNotNull($this->usuarioRepository);
    }

    public function test_user_repository_create(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $user = $this->usuarioRepository->create($userData);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['role'], $user->role->value);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $user->password));
    }

    public function test_user_repository_find_by_email_exists(): void
    {
        $this->test_user_repository_create();

        $userData = [
            'name' => 'Find User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $user = $this->usuarioRepository->create($userData);

        $this->assertNull($user);
    }

    public function test_user_repository_create_params_empty()
    {
        $user = $this->usuarioRepository->create([]);
        $this->assertNull($user);
    }

    public function test_user_repository_update(): void
    {
        $userData = [
            'name' => 'Update User',
            'email' => 'updateuser@example.com',
            'password' => 'password',
            'role' => 'cliente',
        ];

        $user = $this->usuarioRepository->create($userData);

        $updatedUser = $this->usuarioRepository->update($user->id, [
            'name' => 'Updated User',
            'email' => 'updateduser@example.com',
            'password' => 'newpassword',
            'role' => 'administrador',
        ]);

        $this->assertNotNull($updatedUser);
        $this->assertEquals('Updated User', $updatedUser->name);
        $this->assertEquals('updateduser@example.com', $updatedUser->email);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword', $updatedUser->password));
        $this->assertEquals('administrador', $updatedUser->role->value);
    }

    public function test_user_repository_update_nonexistent(): void
    {
        $updatedUser = $this->usuarioRepository->update(9999, [
            'name' => 'Nonexistent User',
            'email' => 'nonexistentuser@example.com',
            'password' => 'newpassword',
            'role' => 'administrador',
        ]);

        $this->assertNull($updatedUser);
    }

    public function test_user_repository_delete(): void
    {
        $user = $this->mockUsuarioAdmin();

        $this->assertNotNull($user);

        $this->usuarioRepository->delete($user->id);

        $deletedUser = $this->usuarioRepository->findById($user->id);
        $this->assertNull($deletedUser);
    }

    public function test_user_repository_delete_nonexistent(): void
    {
        $deletedUser = $this->usuarioRepository->delete(9999);
        $this->assertNull($deletedUser);
    }
}
