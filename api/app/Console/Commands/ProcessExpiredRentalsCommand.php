<?php

namespace App\Console\Commands;

use App\Jobs\Rental\SendExpiredRentalNotification;
use App\Services\Rental\RentalPenaltyService;
use Illuminate\Console\Command;

class ProcessExpiredRentalsCommand extends Command
{
    protected $signature = 'rentals:process-expired';

    protected $description = 'Processa multas de locações expiradas e envia notificações por email';

    protected $rentalPenaltyService;

    public function __construct(RentalPenaltyService $rentalPenaltyService)
    {
        parent::__construct();
        $this->rentalPenaltyService = $rentalPenaltyService;
    }

    public function handle(): int
    {
        try {
            $expiredRentals = $this->rentalPenaltyService->getExpiredRentalsForNotification();

            if ($expiredRentals->isEmpty()) {
                return Command::SUCCESS;
            }

            $result = $this->rentalPenaltyService->processExpiredRentals();

            if ($result['failed'] > 0) {
                $this->warn("Falhas ao atualizar: {$result['failed']}");
            }

            foreach ($expiredRentals as $rental) {
                SendExpiredRentalNotification::dispatch(
                    $rental,
                    app(RentalPenaltyService::class)
                );
            }
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao processar locações expiradas: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
