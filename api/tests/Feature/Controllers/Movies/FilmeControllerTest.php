<?php

namespace Tests\Feature\Controllers\Movies;

class FilmeControllerTest extends \Tests\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/movies');

        $response->assertStatus(401);
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/movies', []);

        $response->assertStatus(401);
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/v1/movies/some-uuid', []);

        $response->assertStatus(401);
    }

    public function test_destroy_requires_authentication(): void
    {
        $response = $this->deleteJson('/api/v1/movies/some-uuid');

        $response->assertStatus(401);
    }

    public function test_index_returns_movies_for_authenticated_user(): void
    {
        $user = $this->mockUsuarioAdmin();

        $response = $this->withHeaders($this->getAuthHeaders($user))->getJson('/api/v1/movies');

        $response->assertStatus(200);
    }

    public function test_index_returns_movies_for_authenticated_user_atendente(): void
    {
        $user = $this->mockUsuarioAtendente();

        $response = $this->withHeaders($this->getAuthHeaders($user))->getJson('/api/v1/movies');

        $response->assertStatus(200);
    }

    public function test_index_forbids_access_for_authenticated_user_cliente(): void
    {
        $user = $this->mockUsuarioCliente();

        $response = $this->withHeaders($this->getAuthHeaders($user))->getJson('/api/v1/movies');

        $response->assertStatus(200);
    }

    public function test_store_creates_movie_for_authenticated_user(): void
    {
        $user = $this->mockUsuarioAdmin();

        $movieData = [
            'title' => 'Inception',
            'description' => 'Um thriller alucinante',
            'release_year' => 2010,
            'genre' => 'Sci-Fi',
            'rental_price' => 4.99,
            'quantity' => 10,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/v1/movies', $movieData);

        $response->assertStatus(201);

        $this->assertEquals($movieData['title'], $response['title']);
        $this->assertEquals($movieData['description'], $response['description']);
        $this->assertEquals($movieData['release_year'], $response['release_year']);
        $this->assertEquals($movieData['genre'], $response['genre']);
        $this->assertEquals($movieData['rental_price'], $response['rental_price']);
        $this->assertEquals($movieData['quantity'], $response['quantity']);
    }

    public function test_store_forbids_creation_for_authenticated_user_without_title(): void
    {
        $user = $this->mockUsuarioAdmin();

        $movieData = [
            'title' => '',
            'description' => 'Um thriller alucinante',
            'release_year' => 2010,
            'genre' => 'Sci-Fi',
            'rental_price' => 4.99,
            'quantity' => 10,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/v1/movies', $movieData);

        $response->assertStatus(422);
        $this->assertArrayHasKey('title', $response->json('errors'));
    }

    public function test_store_forbids_creation_for_authenticated_user_without_data(): void
    {
        $user = $this->mockUsuarioAdmin();

        $movieData = [
            'title' => '',
            'description' => '',
            'release_year' => '',
            'genre' => '',
            'rental_price' => '',
            'quantity' => '',
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/v1/movies', $movieData);

        $response->assertStatus(422);
        $this->assertArrayHasKey('title', $response->json('errors'));
        $this->assertArrayHasKey('description', $response->json('errors'));
        $this->assertArrayHasKey('release_year', $response->json('errors'));
        $this->assertArrayHasKey('genre', $response->json('errors'));
        $this->assertArrayHasKey('rental_price', $response->json('errors'));
        $this->assertArrayHasKey('quantity', $response->json('errors'));
    }

    public function test_store_forbids_creation_for_authenticated_user_atendente(): void
    {
        $user = $this->mockUsuarioAtendente();

        $movieData = [
            'title' => 'Inception',
            'description' => 'Um thriller alucinante',
            'release_year' => 2010,
            'genre' => 'Sci-Fi',
            'rental_price' => 4.99,
            'quantity' => 10,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/v1/movies', $movieData);

        $response->assertStatus(403);
    }

    public function test_store_forbids_creation_for_authenticated_user_cliente(): void
    {
        $user = $this->mockUsuarioCliente();

        $movieData = [
            'title' => 'Inception',
            'description' => 'Um thriller alucinante',
            'release_year' => 2010,
            'genre' => 'Sci-Fi',
            'rental_price' => 4.99,
            'quantity' => 10,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->postJson('/api/v1/movies', $movieData);

        $response->assertStatus(403);
    }

    public function test_update_updates_movie_for_authenticated_user(): void
    {
        $user = $this->mockUsuarioAdmin();

        $movie = $this->mockFilme();

        $updateData = [
            'title' => 'Inception Updated',
            'description' => 'Um thriller alucinante e atualizado.',
            'release_year' => 2011,
            'genre' => 'Sci-Fi Updated',
            'rental_price' => 5.99,
            'quantity' => 15,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/v1/movies/{$movie->uuid}", $updateData);

        $response->assertStatus(200);

        $this->assertEquals($updateData['title'], $response['title']);
        $this->assertEquals($updateData['description'], $response['description']);
        $this->assertEquals($updateData['release_year'], $response['release_year']);
        $this->assertEquals($updateData['genre'], $response['genre']);
        $this->assertEquals($updateData['rental_price'], $response['rental_price']);
        $this->assertEquals($updateData['quantity'], $response['quantity']);
    }

    public function test_update_forbids_update_for_unauthenticated_user(): void
    {
        $movie = $this->mockFilme();

        $updateData = [
            'title' => 'Inception Updated',
            'description' => 'Um thriller alucinante e atualizado.',
            'release_year' => 2011,
            'genre' => 'Sci-Fi Updated',
            'rental_price' => 5.99,
            'quantity' => 15,
        ];

        $response = $this->putJson("/api/v1/movies/{$movie->uuid}", $updateData);

        $response->assertStatus(401);
    }

    public function test_update_forbids_update_for_authenticated_user_without_data(): void
    {
        $user = $this->mockUsuarioAtendente();

        $movie = $this->mockFilme();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/v1/movies/{$movie->uuid}", []);

        $response->assertStatus(403);
    }

    public function test_update_forbids_update_for_authenticated_user_cliente(): void
    {
        $user = $this->mockUsuarioCliente();

        $movie = $this->mockFilme();

        $updateData = [
            'title' => 'Inception Updated',
            'description' => 'Um thriller alucinante e atualizado.',
            'release_year' => 2011,
            'genre' => 'Sci-Fi Updated',
            'rental_price' => 5.99,
            'quantity' => 15,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/v1/movies/{$movie->uuid}", $updateData);

        $response->assertStatus(403);
    }

    public function test_update_forbids_update_for_authenticated_user_atendente(): void
    {
        $user = $this->mockUsuarioAtendente();

        $movie = $this->mockFilme();

        $updateData = [
            'title' => 'Inception Updated',
            'description' => 'Um thriller alucinante e atualizado.',
            'release_year' => 2011,
            'genre' => 'Sci-Fi Updated',
            'rental_price' => 5.99,
            'quantity' => 15,
        ];

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->putJson("/api/v1/movies/{$movie->uuid}", $updateData);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_movie_for_authenticated_user(): void
    {
        $user = $this->mockUsuarioAdmin();

        $movie = $this->mockFilme();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/v1/movies/{$movie->uuid}");

        $response->assertStatus(204);
    }

    public function test_destroy_forbids_deletion_for_unauthenticated_user(): void
    {
        $movie = $this->mockFilme();

        $response = $this->deleteJson("/api/v1/movies/{$movie->uuid}");

        $response->assertStatus(401);
    }

    public function test_destroy_forbids_deletion_for_authenticated_user_atendente(): void
    {
        $user = $this->mockUsuarioAtendente();

        $movie = $this->mockFilme();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/v1/movies/{$movie->uuid}");

        $response->assertStatus(403);
    }

    public function test_destroy_forbids_deletion_for_authenticated_user_cliente(): void
    {
        $user = $this->mockUsuarioCliente();

        $movie = $this->mockFilme();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/v1/movies/{$movie->uuid}");

        $response->assertStatus(403);
    }

    public function test_destroy_forbids_deletion_of_nonexistent_movie(): void
    {
        $user = $this->mockUsuarioAdmin();

        $response = $this->withHeaders($this->getAuthHeaders($user))
            ->deleteJson("/api/v1/movies/asdasdasdasd");

        $response->assertStatus(422);
    }
}
