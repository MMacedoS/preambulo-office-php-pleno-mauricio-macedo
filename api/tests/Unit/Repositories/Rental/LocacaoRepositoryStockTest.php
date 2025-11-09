<?php

namespace Tests\Unit\Repositories\Rental;

use App\Models\Movies\Filme;
use App\Models\Rental\Locacao;
use App\Models\User;
use App\Enums\UserRole;
use App\Repositories\Entities\Rental\LocacaoFilmesRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocacaoRepositoryStockTest extends TestCase
{
    use RefreshDatabase;

    protected LocacaoFilmesRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = LocacaoFilmesRepository::getInstance();
    }

    public function test_attach_movies_decrements_stock(): void
    {
        $usuario = User::factory()->create(['role' => UserRole::CLIENTE]);
        $locacao = Locacao::factory()->create(['usuario_id' => $usuario->id]);
        $filme = Filme::factory()->create(['quantidade' => 10]);

        $this->repository->attachMoviesToLocacao($locacao->id, [$filme->id]);

        $this->assertEquals(9, $filme->fresh()->quantidade);
    }

    public function test_attach_multiple_movies_decrements_stock(): void
    {
        $usuario = User::factory()->create(['role' => UserRole::CLIENTE]);
        $locacao = Locacao::factory()->create(['usuario_id' => $usuario->id]);
        $filme1 = Filme::factory()->create(['quantidade' => 10]);
        $filme2 = Filme::factory()->create(['quantidade' => 15]);

        $this->repository->attachMoviesToLocacao($locacao->id, [$filme1->id, $filme2->id]);

        $this->assertEquals(9, $filme1->fresh()->quantidade);
        $this->assertEquals(14, $filme2->fresh()->quantidade);
    }

    public function test_detach_movies_increments_stock(): void
    {
        $usuario = User::factory()->create(['role' => UserRole::CLIENTE]);
        $locacao = Locacao::factory()->create(['usuario_id' => $usuario->id]);
        $filme = Filme::factory()->create(['quantidade' => 8]);

        $locacao->filmes()->attach($filme->id, ['quantidade' => 1, 'preco_unitario' => 10]);

        $this->repository->detachMoviesFromLocacao($locacao->id, [$filme->id]);

        $this->assertEquals(10, $filme->fresh()->quantidade);
    }

    public function test_detach_multiple_movies_increments_stock(): void
    {
        $usuario = User::factory()->create(['role' => UserRole::CLIENTE]);
        $locacao = Locacao::factory()->create(['usuario_id' => $usuario->id]);
        $filme1 = Filme::factory()->create(['quantidade' => 8]);
        $filme2 = Filme::factory()->create(['quantidade' => 12]);

        $locacao->filmes()->attach($filme1->id, ['quantidade' => 1, 'preco_unitario' => 10]);
        $locacao->filmes()->attach($filme2->id, ['quantidade' => 1, 'preco_unitario' => 15]);

        $this->repository->detachMoviesFromLocacao($locacao->id, [$filme1->id, $filme2->id]);

        $this->assertEquals(10, $filme1->fresh()->quantidade);
        $this->assertEquals(14, $filme2->fresh()->quantidade);
    }

    public function test_attach_with_custom_quantity(): void
    {
        $usuario = User::factory()->create(['role' => UserRole::CLIENTE]);
        $locacao = Locacao::factory()->create(['usuario_id' => $usuario->id]);
        $filme = Filme::factory()->create(['quantidade' => 20]);

        $this->repository->attachMoviesToLocacao(
            $locacao->id,
            [(object)['id' => $filme->id, 'quantidade' => 5]]
        );

        $this->assertEquals(15, $filme->fresh()->quantidade);
    }
}
