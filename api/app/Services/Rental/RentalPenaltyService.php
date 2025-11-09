<?php

namespace App\Services\Rental;

use App\Models\Rental\Locacao;
use App\Repositories\Entities\Rental\LocacaoRepository;

class RentalPenaltyService
{
    protected $locacaoRepository;

    public function __construct(LocacaoRepository $locacaoRepository)
    {
        $this->locacaoRepository = $locacaoRepository;
    }

    public function processExpiredRentals(): array
    {
        return $this->locacaoRepository->updateAllExpiredPenalties();
    }

    public function getExpiredRentalsForNotification()
    {
        return $this->locacaoRepository->getExpiredRentals();
    }

    public function formatRentalDataForEmail(Locacao $locacao): array
    {
        $daysOverdue = (int) abs(now()->diffInDays($locacao->data_devolucao));
        $numberOfMovies = $locacao->filmes()->count();
        $currentPenalty = $locacao->multa;
        $penaltyPerDay = $numberOfMovies * 5.00;

        $dataDevolucao = is_string($locacao->data_devolucao)
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $locacao->data_devolucao)
            : $locacao->data_devolucao;

        return [
            'usuario_nome' => $locacao->usuario->name ?? 'Cliente',
            'usuario_email' => $locacao->usuario->email,
            'locacao_uuid' => $locacao->uuid,
            'locacao_id' => $locacao->id,
            'data_devolucao_prevista' => $dataDevolucao->format('d/m/Y'),
            'dias_atraso' => $daysOverdue,
            'quantidade_filmes' => $numberOfMovies,
            'multa_diaria' => $penaltyPerDay,
            'multa_total_acumulada' => $currentPenalty,
            'filmes' => $locacao->filmes->map(function ($filme) {
                return [
                    'titulo' => $filme->titulo,
                    'quantidade' => $filme->pivot->quantidade,
                ];
            })->toArray(),
        ];
    }
}
