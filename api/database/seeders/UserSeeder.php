<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Admin Sistema',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMINISTRADOR,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Maria Atendente',
            'email' => 'atendente@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ATENDENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Ana Atendente',
            'email' => 'ana@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ATENDENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'João Cliente',
            'email' => 'cliente@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Pedro Cliente',
            'email' => 'pedro@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Carlos Silva',
            'email' => 'carlos@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Fernanda Costa',
            'email' => 'fernanda@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Lucas Oliveira',
            'email' => 'lucas@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Beatriz Santos',
            'email' => 'beatriz@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Rafael Pereira',
            'email' => 'rafael@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Juliana Martins',
            'email' => 'juliana@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);

        User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Thiago Rocha',
            'email' => 'thiago@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENTE,
            'email_verified_at' => now(),
        ]);
    }
}
