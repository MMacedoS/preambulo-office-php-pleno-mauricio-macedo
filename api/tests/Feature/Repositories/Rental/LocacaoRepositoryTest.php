<?php

namespace Tests\Feature\Repositories\Rental;

class LocacaoRepositoryTest extends \Tests\TestCase
{
    protected $locacaoRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->locacaoRepository = app(\App\Repositories\Entities\Rental\LocacaoRepository::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_locacao_repository_instance(): void
    {
        $this->assertInstanceOf(
            \App\Repositories\Entities\Rental\LocacaoRepository::class,
            $this->locacaoRepository
        );

        $this->assertNotNull($this->locacaoRepository);
    }

    public function test_attach_and_detach_movies_to_locacao(): void
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

        $this->locacaoRepository->attachMoviesToLocacao($locacao->id, [
            $filme1,
            $filme2,
        ]);

        $locacaoRefresh = $this->locacaoRepository->findById($locacao->id);
        $this->assertCount(2, $locacaoRefresh->filmes);
        $this->assertNotNull($locacaoRefresh->filmes[0]['pivot']);
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

        $this->locacaoRepository->attachMoviesToLocacao($locacao->id, [
            $filme1,
            $filme2,
        ]);

        $this->locacaoRepository->detachMoviesFromLocacao($locacao->id, [
            $filme1,
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
        $filme1 = $this->mockFilme();
        $filme2 = $this->mockFilme();

        $this->locacaoRepository->attachMoviesToLocacao($locacao->id, [
            (object)[
                'id' => $filme1->id,
                'quantidade' => 2,
                'preco_unitario' => 15.00,
            ],
            (object)[
                'id' => $filme2->id,
                'quantidade' => 1,
                'preco_unitario' => 25.00,
            ],
        ]);

        $totalValue = $this->locacaoRepository->calculateTotalValue($locacao->id);
        $this->assertEquals(55.00, $totalValue);
    }
}
