<?php

namespace Tests\Feature\Controllers\Rental;

use Illuminate\Support\Facades\DB;

class LocacaoControllerTest extends \Tests\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_create_locacao_endpoint(): void
    {
        $user = $this->mockUsuarioAdmin();
        $client = $this->mockUsuarioCliente();
        $filme1 = $this->mockFilme(['quantidade' => 5]);
        $filme2 = $this->mockFilme(['quantidade' => 3]);
        $payload = [
            'client_id' => $client->uuid,
            'movies' => [$filme1->uuid, $filme2->uuid],
            'return_date' => now()->addDays(5)->toDateString(),
            'rental_date' => now()->toDateString(),
            'status' => 'ativo',
            'total_value' => 50.00,
        ];

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->postJson('/api/v1/rentals', $payload);

        $response->assertStatus(201);
        $responseData = $response->json('data');
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertEquals($client->uuid, $responseData['client']['id']);
        $this->assertCount(2, $responseData['movies']);
        $this->assertEquals('ativo', $responseData['status']);
        $expectedTotal = $filme1->valor_aluguel + $filme2->valor_aluguel;
        $this->assertEqualsWithDelta($expectedTotal, $responseData['total_value'], 0.01);
    }

    public function test_create_locacao_endpoint_with_invalid_data(): void
    {
        $user = $this->mockUsuarioAdmin();
        $payload = [
            'client_id' => '550e8400-e29b-41d4-a716-446655440000',
            'movies' => [],
            'return_date' => '2023-01-01',
            'rental_date' => '2023-01-05',
            'status' => 'unknown_status',
            'total_value' => -10.00,
        ];

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->postJson('/api/v1/rentals', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'client_id',
            'return_date',
            'status',
            'total_value',
        ]);
    }

    public function test_create_locacao_endpoint_unauthenticated(): void
    {
        $payload = [
            'client_id' => 'some-uuid',
            'movies' => [1, 2],
            'return_date' => now()->addDays(5)->toDateString(),
            'rental_date' => now()->toDateString(),
            'status' => 'ativo',
            'total_value' => 50.00,
        ];

        $response = $this->postJson('/api/v1/rentals', $payload);

        $response->assertStatus(401);
    }

    public function test_create_locacao_endpoint_atendente(): void
    {
        $user = $this->mockUsuarioAtendente();
        $client = $this->mockUsuarioCliente();

        $filme1 = $this->mockFilme(['quantidade' => 5]);
        $filme2 = $this->mockFilme(['quantidade' => 3]);

        $payload = [
            'client_id' => $client->uuid,
            'movies' => [$filme1->uuid, $filme2->uuid],
            'return_date' => now()->addDays(5)->toDateString(),
            'rental_date' => now()->toDateString(),
            'status' => 'ativo',
            'total_value' => 50.00,
        ];

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->postJson('/api/v1/rentals', $payload);

        $response->assertStatus(201);
        $responseData = $response->json('data');
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertEquals($client->uuid, $responseData['client']['id']);
        $this->assertCount(2, $responseData['movies']);
        $this->assertEquals('ativo', $responseData['status']);
        $expectedTotal = $filme1->valor_aluguel + $filme2->valor_aluguel;
        $this->assertEqualsWithDelta($expectedTotal, $responseData['total_value'], 0.01);
    }

    public function test_create_locacao_endpoint_with_client(): void
    {
        $client = $this->mockUsuarioCliente();
        $filme1 = $this->mockFilme(['quantidade' => 5]);
        $filme2 = $this->mockFilme(['quantidade' => 3]);
        $payload = [
            'client_id' => $client->uuid,
            'movies' => [$filme1->uuid, $filme2->uuid],
            'return_date' => now()->addDays(5)->toDateString(),
            'rental_date' => now()->toDateString(),
            'status' => 'ativo',
            'total_value' => 50.00,
        ];

        $response = $this->withHeaders(
            $this->getAuthHeaders($client)
        )->postJson('/api/v1/rentals', $payload);

        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Acesso negado'
        ]);
    }

    public function test_create_locacao_endpoint_with_insufficient_movie_stock(): void
    {
        $user = $this->mockUsuarioAdmin();
        $client = $this->mockUsuarioCliente();
        $filme1 = $this->mockFilme(['quantidade' => 0]);
        $filme2 = $this->mockFilme(['quantidade' => 3]);
        $payload = [
            'client_id' => $client->uuid,
            'movies' => [$filme1->uuid, $filme2->uuid],
            'return_date' => now()->addDays(5)->toDateString(),
            'rental_date' => now()->toDateString(),
            'status' => 'ativo',
            'total_value' => 50.00,
        ];

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->postJson('/api/v1/rentals', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'movies',
        ]);
    }

    public function test_show_locacao_endpoint(): void
    {
        $user = $this->mockUsuarioAdmin();
        $locacao = $this->mockLocacoesWithMovies(['data_devolucao' => now()->subDays(5)->toDateString(), 'data_inicio' => now()->subDays(10)->toDateString()]);

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->getJson("/api/v1/rentals/{$locacao->uuid}");

        $response->assertStatus(200);
        $responseData = $response->json('data');
        $this->assertEquals($locacao->uuid, $responseData['id']);
        $this->assertEquals($locacao->id, $responseData['code']);
        $this->assertEquals($locacao->usuario->uuid, $responseData['client']['id']);
        $this->assertCount(2, $responseData['movies']);
        $this->assertEquals($locacao->status, $responseData['status']);
        $this->assertEqualsWithDelta($locacao->valor_total, $responseData['total_value'], 0.01);
        $this->assertEqualsWithDelta(50.00, $responseData['penalty'], 0.01);
    }

    public function test_rental_active_and_late_returns(): void
    {
        $client = $this->mockUsuarioCliente("cliente1@gmail.com");
        $this->mockLocacoesWithMovies([
            'usuario_id' => $client->id,
            'status' => 'ativo',
            'data_devolucao' => now()->addDays(3)->toDateString(),
        ], $client);
        $this->mockLocacoesWithMovies([
            'usuario_id' => $client->id,
            'status' => 'atrasado',
            'data_devolucao' => now()->subDays(2)->toDateString(),
        ], $client);

        $response = $this->withHeaders(
            $this->getAuthHeaders($client)
        )->getJson('/api/v1/rentals/info-rentals');

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertCount(2, $responseData['data']);
    }

    public function test_rental_update_with_authenticated(): void
    {
        $this->test_create_locacao_endpoint_atendente();

        $filme1 = $this->mockFilme(['quantidade' => 5]);
        $client = $this->mockUsuarioCliente();

        $payload = [
            'client_id' => $client->uuid,
            'movies' => [$filme1->uuid],
            'return_date' => now()->addDays(5)->toDateString(),
            'rental_date' => now()->toDateString(),
            'status' => 'ativo',
            'total_value' => 50.00,
        ];

        $user = $this->mockUsuarioAdmin();

        $locacao = DB::table('locacoes')->first();

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->putJson('/api/v1/rentals/' . $locacao->uuid, $payload);

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertEquals($locacao->uuid, $responseData['data']['id']);
        $this->assertEquals($locacao->id, $responseData['data']['code']);
        $this->assertCount(1, $responseData['data']['movies']);
        $this->assertEquals($locacao->status, $responseData['data']['status']);
    }

    public function test_rental_delete(): void
    {
        $this->test_create_locacao_endpoint_atendente();

        $user = $this->mockUsuarioAdmin();

        $locacao = DB::table('locacoes')->first();

        $response = $this->withHeaders(
            $this->getAuthHeaders($user)
        )->deleteJson('/api/v1/rentals/' . $locacao->uuid);

        $response->assertStatus(422);
    }
}
