<?php

namespace Tests\Unit\Repositories\Rental;

use App\Models\Movies\Filme;
use App\Repositories\Entities\Rental\LocacaoFilmesRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocacaoFilmesRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected LocacaoFilmesRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = LocacaoFilmesRepository::getInstance();
    }

    public function test_validate_movie_stock_with_available_movies(): void
    {
        $filme1 = Filme::factory()->create(['quantidade' => 5]);
        $filme2 = Filme::factory()->create(['quantidade' => 3]);

        $result = $this->repository->validateMovieStock([$filme1->uuid, $filme2->uuid]);

        $this->assertNull($result);
    }

    public function test_validate_movie_stock_with_insufficient_stock(): void
    {
        $filme1 = Filme::factory()->create(['quantidade' => 0]);
        $filme2 = Filme::factory()->create(['quantidade' => 3]);

        $result = $this->repository->validateMovieStock([$filme1->uuid, $filme2->uuid]);

        $this->assertNotNull($result);
        $this->assertContains($filme1->id, $result);
        $this->assertNotContains($filme2->id, $result);
    }

    public function test_validate_movie_stock_all_insufficient(): void
    {
        $filme1 = Filme::factory()->create(['quantidade' => 0]);
        $filme2 = Filme::factory()->create(['quantidade' => 0]);

        $result = $this->repository->validateMovieStock([$filme1->uuid, $filme2->uuid]);

        $this->assertNotNull($result);
        $this->assertCount(2, $result);
        $this->assertContains($filme1->id, $result);
        $this->assertContains($filme2->id, $result);
    }

    public function test_get_available_movies(): void
    {
        $filme1 = Filme::factory()->create(['quantidade' => 5]);
        $filme2 = Filme::factory()->create(['quantidade' => 0]);
        $filme3 = Filme::factory()->create(['quantidade' => 2]);

        $available = $this->repository->getAvailableMovies(
            [
                $filme1->id,
                $filme2->id,
                $filme3->id
            ]
        );

        $this->assertCount(2, $available);
        $this->assertTrue($available->contains($filme1));
        $this->assertFalse($available->contains($filme2));
        $this->assertTrue($available->contains($filme3));
    }

    public function test_get_total_quantity_available(): void
    {
        $filme = Filme::factory()->create(['quantidade' => 10]);

        $quantity = $this->repository->getTotalQuantityAvailable($filme->id);

        $this->assertEquals(10, $quantity);
    }

    public function test_get_total_quantity_available_non_existent(): void
    {
        $quantity = $this->repository->getTotalQuantityAvailable(9999);

        $this->assertEquals(0, $quantity);
    }
}
