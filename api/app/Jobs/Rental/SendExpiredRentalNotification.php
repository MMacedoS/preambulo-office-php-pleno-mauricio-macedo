<?php

namespace App\Jobs\Rental;

use App\Mail\Rental\ExpiredRentalNotificationMail;
use App\Models\Rental\Locacao;
use App\Services\Rental\RentalPenaltyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendExpiredRentalNotification implements ShouldQueue
{
    use Queueable;

    protected $locacao;
    protected $rentalPenaltyService;

    public function __construct(Locacao $locacao, RentalPenaltyService $rentalPenaltyService)
    {
        $this->locacao = $locacao;
        $this->rentalPenaltyService = $rentalPenaltyService;
    }

    public function handle(): void
    {
        try {
            $emailData = $this->rentalPenaltyService->formatRentalDataForEmail($this->locacao);

            Mail::send(new ExpiredRentalNotificationMail($emailData));

            Log::info('Email de locação expirada enviado com sucesso', [
                'locacao_id' => $this->locacao->id,
                'usuario_email' => $this->locacao->usuario->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação de locação expirada', [
                'locacao_id' => $this->locacao->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
