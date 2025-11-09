<?php

namespace App\Repositories\Entities\Rental;

use App\Models\Rental\Locacao;
use App\Repositories\Contracts\Rental\ILocacaoRepository;
use App\Repositories\Traits\CacheTrait;
use App\Repositories\Traits\QueryBuilderTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\SingletonTrait;

class LocacaoRepository implements ILocacaoRepository
{
    protected $model;

    use SingletonTrait, ServiceTrait, CacheTrait, QueryBuilderTrait;

    public function __construct()
    {
        $this->model = new Locacao();
    }

    public function attachMoviesToLocacao(string $locacaoId, array $filmes): void
    {
        if (empty($filmes)) {
            return;
        }

        $locacao = $this->findById($locacaoId);
        if (is_null($locacao)) {
            return;
        }

        foreach ($filmes as $filme) {
            $quantidade = is_object($filme) ? ($filme->quantidade ?? 1) : ($filme['quantidade'] ?? 1);
            $preco = is_object($filme) ? ($filme->preco_unitario ?? 0) : ($filme['preco_unitario'] ?? 0);

            $locacao->filmes()->attach(
                $filme->id,
                [
                    'quantidade' => $quantidade,
                    'preco_unitario' => $preco,
                ]
            );
        }

        $this->updateLocacaoTotalValue($locacaoId);
    }

    public function detachMoviesFromLocacao(string $locacaoId, array $filmes): void
    {
        if (empty($filmes)) {
            return;
        }

        $locacao = $this->findById($locacaoId);
        if (is_null($locacao)) {
            return;
        }

        foreach ($filmes as $filme) {
            $locacao->filmes()->detach($filme->id);
        }

        // Atualizar valor_total da locação
        $this->updateLocacaoTotalValue($locacaoId);
    }

    public function calculateTotalValue(string $locacaoId): float
    {
        $locacao = $this->findById($locacaoId);
        if (is_null($locacao)) {
            return 0.0;
        }

        return $locacao->filmes()
            ->get()
            ->sum(function ($filme) {
                return $filme->pivot->quantidade * $filme->pivot->preco_unitario;
            });
    }

    public function updateLocacaoTotalValue(string $locacaoId): void
    {
        $total = $this->calculateTotalValue($locacaoId);
        $locacao = $this->findById($locacaoId);
        if (!is_null($locacao)) {
            $locacao->update(['valor_total' => $total]);
        }
    }

    public function findAll(array $criteria = [], array $orderBy = [], array $orWhereCriteria = [])
    {
        if (app()->environment('testing')) {
            return $this->buildQuery($criteria, $orderBy, $orWhereCriteria)->get();
        }

        $cacheKey = $this->makeCacheKey($criteria, $orderBy, $orWhereCriteria);

        return $this->getFromCacheOrFetch(
            $cacheKey,
            fn() => $this->buildQuery($criteria, $orderBy, $orWhereCriteria)->get(),
            1800
        );
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $locacao = $this->model->create($data);
            if (is_null($locacao->id)) {
                return null;
            }
            return $locacao;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $locacao = $this->findById($id);
            if (is_null($locacao)) {
                return null;
            }

            $locacao->update($data);
            return $locacao;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function delete(int $id)
    {
        try {
            $locacao = $this->findById($id);
            if (is_null($locacao)) {
                return false;
            }

            $locacao->delete();
            return $locacao;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function rentalExpiredsCount(): int
    {
        $now = now();
        return $this->model
            ->where('data_devolucao', '<', $now)
            ->where('status', 'ativo')
            ->count();
    }

    public function rentalActiveCount(): int
    {
        return $this->model
            ->where('status', 'ativo')
            ->count();
    }

    public function rentalCompletedCount(): int
    {
        return $this->model
            ->where('status', 'concluido')
            ->count();
    }

    public function getExpiredRentals()
    {
        return $this->model
            ->where('data_devolucao', '<', now()->toDateString())
            ->where('status', 'ativo')
            ->with('usuario', 'filmes')
            ->get();
    }

    public function calculateDailyPenalty(string $locacaoId): float
    {
        $locacao = $this->findById($locacaoId);
        if (is_null($locacao)) {
            return 0.0;
        }

        if ($locacao->data_devolucao >= now()->toDateString()) {
            return 0.0;
        }

        $today = now()->toDateString();
        $dataDevolucao = is_string($locacao->data_devolucao)
            ? $locacao->data_devolucao
            : $locacao->data_devolucao->toDateString();

        $daysOverdue = (int) abs((new \DateTime($today))->diff(new \DateTime($dataDevolucao))->days);
        $numberOfMovies = $locacao->filmes()->count();
        $penaltyPerFilm = 5.00;

        return $daysOverdue * $numberOfMovies * $penaltyPerFilm;
    }

    public function updatePenalty(string $locacaoId): bool
    {
        $penalty = $this->calculateDailyPenalty($locacaoId);
        $locacao = $this->findById($locacaoId);

        if (is_null($locacao)) {
            return false;
        }

        try {
            $locacao->update(['multa' => $penalty]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateAllExpiredPenalties(): array
    {
        $expiredRentals = $this->getExpiredRentals();
        $updated = 0;
        $failed = 0;

        foreach ($expiredRentals as $rental) {
            if ($this->updatePenalty($rental->id)) {
                $updated++;
            } else {
                $failed++;
            }
        }

        return [
            'updated' => $updated,
            'failed' => $failed,
            'total' => $expiredRentals->count(),
        ];
    }
}
