<?php

namespace Tests\Feature\Repositories\Movies;

class FilmeRepositoryTest extends \Tests\TestCase
{
    protected $filmeRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->filmeRepository = app(\App\Repositories\Entities\Movies\FilmeRepository::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_filme_repository_instance(): void
    {
        $this->assertInstanceOf(
            \App\Repositories\Entities\Movies\FilmeRepository::class,
            $this->filmeRepository
        );

        $this->assertNotNull($this->filmeRepository);
    }

    public function test_filme_creation(): void
    {
        $filmeData = [
            'titulo' => 'Inception',
            'sinopse' => 'Um thriller alucinante.',
            'quantidade' => 14,
            'ano_lancamento' => 2010,
            'categoria' => 'Sci-Fi',
            'valor_aluguel' => 4.99,
        ];

        $filme = $this->filmeRepository->create($filmeData);

        $this->assertNotNull($filme);
        $this->assertEquals('Inception', $filme->titulo);
        $this->assertEquals('Um thriller alucinante.', $filme->sinopse);
        $this->assertEquals(14, $filme->quantidade);
        $this->assertEquals(2010, $filme->ano_lancamento);
        $this->assertEquals('Sci-Fi', $filme->categoria);
        $this->assertEquals(4.99, $filme->valor_aluguel);
    }

    public function test_filme_creation_with_missing_title(): void
    {
        $filmeData = [
            'sinopse' => 'Um thriller alucinante.',
            'quantidade' => 14,
            'ano_lancamento' => 2010,
            'categoria' => 'Sci-Fi',
            'valor_aluguel' => 4.99,
        ];

        $filme = $this->filmeRepository->create($filmeData);

        $this->assertNull($filme);
    }

    public function test_filme_creation_with_empty_data(): void
    {
        $filmeData = [];

        $filme = $this->filmeRepository->create($filmeData);

        $this->assertNull($filme);
    }

    public function test_filme_find_all(): void
    {
        $filmeData1 = [
            'titulo' => 'Inception',
            'sinopse' => 'Um thriller alucinante.',
            'quantidade' => 14,
            'ano_lancamento' => 2010,
            'categoria' => 'Sci-Fi',
            'valor_aluguel' => 4.99,
        ];

        $filmeData2 = [
            'titulo' => 'The Matrix',
            'sinopse' => 'Um hacker descobre que a realidade é uma simulação',
            'quantidade' => 10,
            'ano_lancamento' => 1999,
            'categoria' => 'Action',
            'valor_aluguel' => 3.99,
        ];

        $this->filmeRepository->create($filmeData1);
        $this->filmeRepository->create($filmeData2);

        $filmes = $this->filmeRepository->findAll();

        $this->assertIsIterable($filmes);
        $this->assertGreaterThanOrEqual(2, count($filmes));
    }

    public function test_filme_find_by_id(): void
    {
        $filmeData = [
            'titulo' => 'Inception',
            'sinopse' => 'Um thriller alucinante.',
            'quantidade' => 14,
            'ano_lancamento' => 2010,
            'categoria' => 'Sci-Fi',
            'valor_aluguel' => 4.99,
        ];

        $createdFilme = $this->filmeRepository->create($filmeData);

        $foundFilme = $this->filmeRepository->findById($createdFilme->id);

        $this->assertNotNull($foundFilme);
        $this->assertEquals($createdFilme->id, $foundFilme->id);
    }

    public function test_filme_find_by_id_not_found(): void
    {
        $foundFilme = $this->filmeRepository->findById(99999);

        $this->assertNull($foundFilme);
    }

    public function test_filme_update(): void
    {
        $filmeData = [
            'titulo' => 'Inception',
            'sinopse' => 'Um thriller alucinante.',
            'quantidade' => 14,
            'ano_lancamento' => 2010,
            'categoria' => 'Sci-Fi',
            'valor_aluguel' => 4.99,
        ];

        $createdFilme = $this->filmeRepository->create($filmeData);

        $updateData = [
            'titulo' => 'Inception Updated',
            'quantidade' => 20,
        ];

        $updatedFilme = $this->filmeRepository->update($createdFilme->id, $updateData);

        $this->assertNotNull($updatedFilme);
        $this->assertEquals('Inception Updated', $updatedFilme->titulo);
        $this->assertEquals(20, $updatedFilme->quantidade);
    }

    public function test_filme_update_not_found(): void
    {
        $updateData = [
            'titulo' => 'Non-existent Movie',
        ];

        $updatedFilme = $this->filmeRepository->update(99999, $updateData);

        $this->assertFalse($updatedFilme);
    }

    public function test_filme_delete(): void
    {
        $filmeData = [
            'titulo' => 'Inception',
            'sinopse' => 'Um thriller alucinante.',
            'quantidade' => 14,
            'ano_lancamento' => 2010,
            'categoria' => 'Sci-Fi',
            'valor_aluguel' => 4.99,
        ];

        $createdFilme = $this->filmeRepository->create($filmeData);

        $deleted = $this->filmeRepository->delete($createdFilme->id);

        $this->assertTrue($deleted);

        $foundFilme = $this->filmeRepository->findById($createdFilme->id);

        $this->assertNull($foundFilme);
    }

    public function test_filme_delete_not_found(): void
    {
        $deleted = $this->filmeRepository->delete(99999);

        $this->assertFalse($deleted);
    }
}
