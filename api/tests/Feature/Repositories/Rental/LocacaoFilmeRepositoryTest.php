<?php

namespace Tests\Feature\Repositories\Rental;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocacaoFilmeRepositoryTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_locacao_filme_repository_instance(): void
    {
        $this->assertTrue(true);
    }
}
