<?php

namespace Tests\Feature\Repositories\Rental;

use App\Repositories\Contracts\Rental\ILocacaoFilmesRepository;
use App\Repositories\Entities\Rental\LocacaoRepository;

class LocacaoRepositoryTest extends \Tests\TestCase
{
    protected $locacaoRepository;
    protected $locacaoFilmesRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->locacaoRepository = app(LocacaoRepository::class);
        $this->locacaoFilmesRepository = app(ILocacaoFilmesRepository::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_locacao_repository_instance(): void
    {
        $this->assertInstanceOf(
            LocacaoRepository::class,
            $this->locacaoRepository
        );

        $this->assertNotNull($this->locacaoRepository);
    }

    public function test_attach_and_detach_movies_to_locacao(): void
    {
        $usuario = $this->mockUsuarioAdmin();
        $locacao = $this->locacaoRepository->create(
            [
                'usuario_id' => $usuario->id,
                'data_inicio' => now()->toDateString(),
                'data_devolucao' => now()->addDays(5)->toDateString(),
                'valor_total' => 0,
                'multa' => 0,
                'status' => 'ativo',
            ]
        );
        $filme1 = $this->mockFilme();
        $filme2 = $this->mockFilme();

        $this->locacaoFilmesRepository->attachMoviesToLocacao($locacao->id, [
            $filme1->uuid,
            $filme2->uuid,
        ]);

        $locacao->refresh();
        $this->assertCount(2, $locacao->filmes);

        $this->locacaoFilmesRepository->detachMoviesFromLocacao($locacao->id, [$filme1->uuid]);

        $locacao->refresh();
        $this->assertCount(1, $locacao->filmes);
    }

    public function test_detach_movies_from_locacao(): void
    {
        $client = $this->mockUsuarioCliente();
        $locacao = $this->mockLocacao(
            [
                'usuario_id' => $client->id,
                'data_inicio' => now(),
                'data_devolucao' => now()->addDays(7),
                'valor_total' => 0,
                'multa' => 0,
                'status' => 'ativo',
            ]
        );
        $filme1 = $this->mockFilme();
        $filme2 = $this->mockFilme();

        $this->locacaoFilmesRepository->attachMoviesToLocacao($locacao->id, [
            $filme1->uuid,
            $filme2->uuid,
        ]);

        $this->locacaoFilmesRepository->detachMoviesFromLocacao($locacao->id, [
            $filme1->uuid,
        ]);

        $locacaoRefresh = $this->locacaoRepository->findById($locacao->id);
        $this->assertCount(1, $locacaoRefresh->filmes);
        $this->assertEquals($filme2->id, $locacaoRefresh->filmes[0]->id);
    }

    public function test_calculate_total_value_of_locacao(): void
    {
        $client = $this->mockUsuarioCliente();
        $locacao = $this->mockLocacao(
            [
                'usuario_id' => $client->id,
                'data_inicio' => now(),
                'data_devolucao' => now()->addDays(7),
                'valor_total' => 0,
                'multa' => 0,
                'status' => 'ativo',
            ]
        );
        $filme1 = $this->mockFilme(['valor_aluguel' => 15.00]);
        $filme2 = $this->mockFilme(['valor_aluguel' => 25.00]);

        $this->locacaoFilmesRepository->attachMoviesToLocacao($locacao->id, [
            $filme1->uuid,
            $filme2->uuid,
        ]);

        $totalValue = $this->locacaoFilmesRepository->calculateTotalValue($locacao->id);
        $this->assertEquals(40.00, $totalValue);
    }

    public function test_update_locacao_total_value(): void
    {
        $client = $this->mockUsuarioCliente();
        $locacao = $this->mockLocacao(
            [
                'usuario_id' => $client->id,
                'data_inicio' => now(),
                'data_devolucao' => now()->addDays(7),
                'valor_total' => 0,
                'multa' => 0,
                'status' => 'ativo',
            ]
        );
        $filme1 = $this->mockFilme(['valor_aluguel' => 10.00]);

        $this->locacaoFilmesRepository->attachMoviesToLocacao($locacao->id, [$filme1->uuid]);
        $this->locacaoFilmesRepository->updateLocacaoTotalValue($locacao->id);

        $locacaoRefresh = $this->locacaoRepository->findById($locacao->id);
        $this->assertEquals(10.00, $locacaoRefresh->valor_total);
    }

    public function test_create_rental()
    {
        $client = $this->mockUsuarioCliente();
        $locacaoData = [
            'usuario_id' => $client->id,
            'data_inicio' => now(),
            'data_devolucao' => now()->addDays(5),
            'status' => 'ativo',
            'valor_total' => 0,
        ];

        $locacao = $this->locacaoRepository->create($locacaoData);

        $this->assertNotNull($locacao);
        $this->assertEquals($client->id, $locacao->usuario_id);
        $this->assertEquals('ativo', $locacao->status);
    }
}
