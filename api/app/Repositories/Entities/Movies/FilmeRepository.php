<?php

namespace App\Repositories\Entities\Movies;

use App\Models\Movies\Filme;
use App\Repositories\Contracts\Movies\IFilmeRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;
use App\Repositories\Traits\QueryBuilderTrait;

class FilmeRepository implements IFilmeRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait, QueryBuilderTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new Filme();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $filme = $this->model->create($data);
            if (is_null($filme->id)) {
                return null;
            }
            return $filme;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findAll(
        array $criteria = [],
        array $orderBy = [],
        array $orWhereCriteria = []
    ) {
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

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return false;
        }

        try {
            $filme = $this->findById($id);
            if (is_null($filme)) {
                return false;
            }

            $filme->update($data);

            return $this->findById($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(int $id)
    {
        $filme = $this->findById($id);
        if (is_null($filme)) {
            return false;
        }

        try {
            $deleted = $filme->delete();
            return $deleted;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findByIdsWithInsufficientStock(array $movieIds): array
    {
        return $this->model->whereIn('id', $movieIds)
            ->where('quantidade', '<=', 0)
            ->pluck('id')
            ->toArray();
    }

    public function findAvailableByIds(array $movieIds)
    {
        return $this->model->whereIn('id', $movieIds)
            ->where('quantidade', '>', 0)
            ->get();
    }

    public function decrementQuantity(int $filmeId, int $quantidade = 1): bool
    {
        try {
            $filme = $this->findById($filmeId);
            if (is_null($filme)) {
                return false;
            }

            $novaQuantidade = max(0, $filme->quantidade - $quantidade);
            return (bool) $filme->update(['quantidade' => $novaQuantidade]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function incrementQuantity(int $filmeId, int $quantidade = 1): bool
    {
        try {
            $filme = $this->findById($filmeId);
            if (is_null($filme)) {
                return false;
            }

            $novaQuantidade = $filme->quantidade + $quantidade;
            return (bool) $filme->update(['quantidade' => $novaQuantidade]);
        } catch (\Exception $e) {
            return false;
        }
    }
}
