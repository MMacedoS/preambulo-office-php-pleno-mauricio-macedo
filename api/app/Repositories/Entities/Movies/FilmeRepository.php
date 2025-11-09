<?php

namespace App\Repositories\Entities\Movies;

use App\Models\films\Filme;
use App\Repositories\Contracts\Movies\IFilmeRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;

class FilmeRepository implements IFilmeRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait;

    public function __construct(protected Filme $model) {}

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
        } catch (\Exception $e) {
            return null;
        }
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
}
