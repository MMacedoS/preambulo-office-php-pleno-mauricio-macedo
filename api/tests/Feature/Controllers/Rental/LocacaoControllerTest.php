<?php

namespace Tests\Feature\Controllers\Rental;


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
            'movies' => [$filme1->id, $filme2->id],
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
            'movies',
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
            'movies' => [$filme1->id, $filme2->id],
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
            'movies' => [$filme1->id, $filme2->id],
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
            'movies' => [$filme1->id, $filme2->id],
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
}
