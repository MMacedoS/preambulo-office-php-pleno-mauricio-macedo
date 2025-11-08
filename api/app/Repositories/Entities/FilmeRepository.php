<?php

namespace App\Repositories\Entities;

use App\Models\films\Filme;
use App\Repositories\Contracts\IFilmeRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;

class FilmeRepository implements IFilmeRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait;

    public function __construct(protected Filme $model) {}

    public function create(array $data)
    {
        $filme = $this->model->create($data);
        $this->setCachedObject($filme, 3600);
        return $filme;
    }

    public function findAll()
    {
        return $this->getFromCacheOrFetch(
            $this->model->getTable() . '_all',
            fn() => $this->model->all(),
            1800
        );
    }

    public function update(int $id, array $data)
    {
        $filme = $this->model->find($id);
        if ($filme) {
            $filme->update($data);
            $this->deleteFromCacheById($id);
            $this->removeCachedItem($this->model->getTable() . '_all');
            $this->setCachedObject($filme, 3600);
            return true;
        }
        return false;
    }

    public function delete(int $id)
    {
        $filme = $this->model->find($id);
        if ($filme) {
            $filme->delete();
            $this->deleteFromCacheById($id);
            $this->removeCachedItem($this->model->getTable() . '_all');
            return true;
        }
        return false;
    }
}
