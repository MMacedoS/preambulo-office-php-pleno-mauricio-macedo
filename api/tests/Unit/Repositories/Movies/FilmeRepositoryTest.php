<?php

namespace Tests\Unit\Repositories\Movies;

use App\Models\Movies\Filme;
use App\Repositories\Entities\Movies\FilmeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected FilmeRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = FilmeRepository::getInstance();
    }

    public function test_decrement_quantity(): void
    {
        $filme = Filme::factory()->create(['quantidade' => 10]);

        $result = $this->repository->decrementQuantity($filme->id, 3);

        $this->assertTrue($result);
        $this->assertEquals(7, $filme->fresh()->quantidade);
    }

    public function test_decrement_quantity_never_goes_below_zero(): void
    {
        $filme = Filme::factory()->create(['quantidade' => 2]);

        $result = $this->repository->decrementQuantity($filme->id, 5);

        $this->assertTrue($result);
        $this->assertEquals(0, $filme->fresh()->quantidade);
    }

    public function test_increment_quantity(): void
    {
        $filme = Filme::factory()->create(['quantidade' => 5]);

        $result = $this->repository->incrementQuantity($filme->id, 3);

        $this->assertTrue($result);
        $this->assertEquals(8, $filme->fresh()->quantidade);
    }

    public function test_decrement_quantity_non_existent_filme(): void
    {
        $result = $this->repository->decrementQuantity(9999, 1);

        $this->assertFalse($result);
    }

    public function test_increment_quantity_non_existent_filme(): void
    {
        $result = $this->repository->incrementQuantity(9999, 1);

        $this->assertFalse($result);
    }
}
