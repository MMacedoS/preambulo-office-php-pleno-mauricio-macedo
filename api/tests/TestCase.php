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

    public function mockUsuarioAdmin($email = 'admin@localfilmes.com')
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => UserRole::ADMINISTRADOR,
        ]);

        return $user;
    }

    public function mockUsuarioAtendente($email = 'atendente@localfilmes.com')
    {
        $user = User::factory()->create([
            'name' => 'Atendente User',
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => UserRole::ATENDENTE,
        ]);

        return $user;
    }

    public function mockUsuarioCliente($email = 'cliente@localfilmes.com')
    {
        $user = User::factory()->create([
            'name' => 'Cliente User',
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => UserRole::CLIENTE,
        ]);
        return $user;
    }

    public function prepareTokenAdmin()
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin.test' . uniqid() . '@localfilmes.com',
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
            'email' => 'atendente.test' . uniqid() . '@localfilmes.com',
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
            'email' => 'cliente.test' . uniqid() . '@localfilmes.com',
            'password' => bcrypt('password'),
            'role' => UserRole::CLIENTE,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function mockFilme(array $movieData = []): Filme
    {
        return Filme::factory()->create($movieData);
    }

    public function getAuthHeaders($user): array
    {
        if ($user instanceof User) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        if (is_array($user) && isset($user['token'])) {
            $token = $user['token'];
        }

        return [
            'Authorization' => "Bearer {$token}",
        ];
    }

    public function mockLocacao(array $locacaoData = [], $status = 'ativo')
    {
        $locacaoData['status'] = $status;
        return \App\Models\Rental\Locacao::factory()->create($locacaoData);
    }

    public function mockLocacaoFilme(array $locacaoFilmeData = [])
    {
        return \App\Models\Rental\LocacaoFilme::factory()->create($locacaoFilmeData);
    }

    public function mockLocacoesWithMovies(array $locacaoData = [], $client = null)
    {
        if (!$client) {
            $client = $this->mockUsuarioCliente();
        }

        $filme1 = $this->mockFilme(['quantidade' => 5, 'valor_aluguel' => 10.00]);
        $filme2 = $this->mockFilme(['quantidade' => 3, 'valor_aluguel' => 15.00]);

        $locacaoData['usuario_id'] = $client->id;
        $locacao = $this->mockLocacao($locacaoData);

        $locacaoFilme1 = $this->mockLocacaoFilme([
            'locacao_id' => $locacao->id,
            'filme_id' => $filme1->id,
            'quantidade' => 1,
            'preco_unitario' => 10.00,
        ]);

        $locacaoFilme2 = $this->mockLocacaoFilme([
            'locacao_id' => $locacao->id,
            'filme_id' => $filme2->id,
            'quantidade' => 1,
            'preco_unitario' => 15.00,
        ]);

        $valorTotal = ($locacaoFilme1->quantidade * $locacaoFilme1->preco_unitario) +
            ($locacaoFilme2->quantidade * $locacaoFilme2->preco_unitario);

        $locacao->update(['valor_total' => $valorTotal]);

        return $locacao->fresh('filmes');
    }
}
