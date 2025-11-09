<?php

namespace Tests\Feature;

use App\Jobs\Rental\SendExpiredRentalNotification;
use App\Models\Rental\Locacao;
use App\Models\User;
use App\Repositories\Entities\Rental\LocacaoRepository;
use App\Services\Rental\RentalPenaltyService;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RentalPenaltySystemTest extends TestCase
{
    protected $locacaoRepository;
    protected $rentalPenaltyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locacaoRepository = app(LocacaoRepository::class);
        $this->rentalPenaltyService = app(RentalPenaltyService::class);
    }

    #[Test]
    public function it_can_calculate_daily_penalty_correctly()
    {
        // Arrange
        $user = User::factory()->create();
        $rental = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(3)->toDateString(),
            'status' => 'ativo',
        ]);

        // Criar 2 filmes e attach à locação
        $filmes = \App\Models\Movies\Filme::factory()->count(2)->create();
        foreach ($filmes as $filme) {
            $rental->filmes()->attach($filme->id, [
                'quantidade' => 1,
                'preco_unitario' => 10.00,
            ]);
        }

        // Act
        $penalty = $this->locacaoRepository->calculateDailyPenalty($rental->id);

        // Assert
        // 3 dias × 2 filmes × 5 = 30
        $this->assertEquals(30.00, $penalty);
    }

    #[Test]
    public function it_can_get_expired_rentals()
    {
        // Arrange
        $user = User::factory()->create();

        // Criar 2 locações expiradas
        $expiredRentals = Locacao::factory()->count(2)->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(1)->toDateString(),
            'status' => 'ativo',
        ]);

        // Criar 1 locação não expirada
        Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->addDays(5)->toDateString(),
            'status' => 'ativo',
        ]);

        // Act
        $result = $this->locacaoRepository->getExpiredRentals();

        // Assert
        $this->assertEquals(2, $result->count());
    }

    #[Test]
    public function it_can_update_penalty()
    {
        // Arrange
        $user = User::factory()->create();
        $rental = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(2)->toDateString(),
            'status' => 'ativo',
            'multa' => 0,
        ]);

        $filmes = \App\Models\Movies\Filme::factory()->count(1)->create();
        foreach ($filmes as $filme) {
            $rental->filmes()->attach($filme->id, [
                'quantidade' => 1,
                'preco_unitario' => 10.00,
            ]);
        }

        // Act
        $result = $this->locacaoRepository->updatePenalty($rental->id);

        // Assert
        $this->assertTrue($result);
        $updatedRental = $this->locacaoRepository->findById($rental->id);
        // 2 dias × 1 filme × 5 = 10
        $this->assertEquals(10.00, $updatedRental->multa);
    }

    #[Test]
    public function it_can_format_email_data()
    {
        // Arrange
        $user = User::factory()->create();
        $rental = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(5)->toDateString(),
            'status' => 'ativo',
            'multa' => 50.00,
        ]);

        $filmes = \App\Models\Movies\Filme::factory()->count(2)->create();
        foreach ($filmes as $filme) {
            $rental->filmes()->attach($filme->id, [
                'quantidade' => 1,
                'preco_unitario' => 10.00,
            ]);
        }

        // Act
        $emailData = $this->rentalPenaltyService->formatRentalDataForEmail($rental);

        // Assert
        $this->assertArrayHasKey('usuario_nome', $emailData);
        $this->assertArrayHasKey('usuario_email', $emailData);
        $this->assertArrayHasKey('locacao_uuid', $emailData);
        $this->assertArrayHasKey('dias_atraso', $emailData);
        $this->assertArrayHasKey('quantidade_filmes', $emailData);
        $this->assertArrayHasKey('multa_total_acumulada', $emailData);
        $this->assertArrayHasKey('filmes', $emailData);
        $this->assertEquals(2, count($emailData['filmes']));
    }

    #[Test]
    public function it_can_dispatch_notification_job()
    {
        // Arrange
        Queue::fake();

        $user = User::factory()->create();
        $rental = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(1)->toDateString(),
            'status' => 'ativo',
        ]);

        // Act
        SendExpiredRentalNotification::dispatch(
            $rental,
            $this->rentalPenaltyService
        );

        // Assert
        Queue::assertPushed(SendExpiredRentalNotification::class);
    }

    #[Test]
    public function it_returns_zero_penalty_for_non_expired_rentals()
    {
        // Arrange
        $user = User::factory()->create();
        $rental = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->addDays(5)->toDateString(),
            'status' => 'ativo',
        ]);

        // Act
        $penalty = $this->locacaoRepository->calculateDailyPenalty($rental->id);

        // Assert
        $this->assertEquals(0.0, $penalty);
    }

    #[Test]
    public function it_can_execute_process_expired_rentals_command()
    {
        // Arrange
        Queue::fake();

        $user = User::factory()->create();

        // Criar 2 locações expiradas
        $rental1 = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(2)->toDateString(),
            'status' => 'ativo',
            'multa' => 0,
        ]);

        $rental2 = Locacao::factory()->create([
            'usuario_id' => $user->id,
            'data_devolucao' => now()->subDays(3)->toDateString(),
            'status' => 'ativo',
            'multa' => 0,
        ]);

        // Attach filmes às locações
        $filmes1 = \App\Models\Movies\Filme::factory()->count(2)->create();
        foreach ($filmes1 as $filme) {
            $rental1->filmes()->attach($filme->id, [
                'quantidade' => 1,
                'preco_unitario' => 10.00,
            ]);
        }

        $filmes2 = \App\Models\Movies\Filme::factory()->count(1)->create();
        foreach ($filmes2 as $filme) {
            $rental2->filmes()->attach($filme->id, [
                'quantidade' => 1,
                'preco_unitario' => 10.00,
            ]);
        }

        // Act - Executar o comando
        $this->artisan('rentals:process-expired')
            ->assertExitCode(0);

        // Assert - Verificar que o comando foi executado com sucesso
        // e que jobs foram despachados
        Queue::assertPushed(SendExpiredRentalNotification::class);
    }
}
