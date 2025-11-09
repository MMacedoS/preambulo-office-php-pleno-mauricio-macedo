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
            $locacao->update(['total' => $total]);
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
}
