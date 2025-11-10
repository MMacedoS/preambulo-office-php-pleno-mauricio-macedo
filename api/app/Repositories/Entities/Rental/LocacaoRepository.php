<?php

namespace App\Repositories\Entities\Rental;

use App\Models\Rental\Locacao;
use App\Repositories\Contracts\Rental\ILocacaoRepository;
use App\Repositories\Entities\Rental\LocacaoFilmesRepository;
use App\Repositories\Traits\CacheTrait;
use App\Repositories\Traits\QueryBuilderTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\SingletonTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocacaoRepository implements ILocacaoRepository
{
    private const PENDING_STATUSES = "atrasado";
    private const ACTIVE_STATUSES = "ativo";
    protected $model;
    protected LocacaoFilmesRepository $locacaoFilmesRepository;

    use SingletonTrait, ServiceTrait, CacheTrait, QueryBuilderTrait;

    public function __construct(LocacaoFilmesRepository $locacaoFilmesRepository)
    {
        $this->model = new Locacao();
        $this->locacaoFilmesRepository = $locacaoFilmesRepository;
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

        if ($this->isRentalStatusPending($data['usuario_id'])) {
            return null;
        }

        try {
            return DB::transaction(function () use ($data) {
                $movies = $data['filmes'] ?? [];
                if (isset($data['filmes'])) {
                    unset($data['filmes']);
                }

                $data['valor_total'] = $data['valor_total'] ?? 0;

                $locacao = $this->model->create($data);
                if (is_null($locacao->id)) {
                    return null;
                }

                if (!empty($movies)) {
                    $this->locacaoFilmesRepository->attachMoviesToLocacao($locacao->id, $movies);
                    $locacao = $this->findById($locacao->id);
                }

                return $locacao;
            });
        } catch (\Exception $e) {
            Log::error('LocacaoRepository::create - Exception: ' . $e->getMessage());
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

            if ($this->hasRelatedMovies($locacao)) {
                return false;
            }

            $locacao->delete();
            return $locacao;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function hasRelatedMovies($locacao): bool
    {
        return $locacao->filmes()->exists();
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
            ->whereIn('status', [self::ACTIVE_STATUSES, self::PENDING_STATUSES])
            ->with('usuario', 'filmes')
            ->get();
    }

    public function calculateDailyPenalty(int $locacaoId): float
    {
        if (is_null($locacaoId)) {
            return 0.0;
        }

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

    public function updatePenalty(int $locacaoId): bool
    {
        $penalty = $this->calculateDailyPenalty($locacaoId);
        $locacao = $this->findById($locacaoId);

        if (is_null($locacao)) {
            return false;
        }

        try {
            $locacao->update([
                'multa' => $penalty,
                'status' => 'atrasado'
            ]);
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

    private function isRentalStatusPending(int $clientId): bool
    {
        $count = $this->model
            ->where('usuario_id', $clientId)
            ->where('status', self::PENDING_STATUSES)
            ->count();

        return $count > 0;
    }

    public function processReturn(int $locacaoId): bool
    {
        $locacao = $this->findById($locacaoId);
        if (is_null($locacao)) {
            return false;
        }

        try {
            DB::transaction(function () use ($locacao) {
                $locacao->update([
                    'status' => 'devolvido',
                    'multa' => $this->calculateDailyPenalty($locacao->id),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('LocacaoRepository::processReturn - Exception: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function getClientActiveRentals(int $ClientId)
    {
        return $this->model
            ->where('usuario_id', $ClientId)
            ->whereIn('status', [self::ACTIVE_STATUSES, self::PENDING_STATUSES])
            ->get();
    }

    public function getClientRentalHistory(int $ClientId)
    {
        return $this->model
            ->where('usuario_id', $ClientId)
            ->with('filmes')
            ->get();
    }
}
