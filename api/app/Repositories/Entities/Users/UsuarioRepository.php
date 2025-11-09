<?php

namespace App\Repositories\Entities\Users;

use App\Models\User;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;
use App\Repositories\Traits\QueryBuilderTrait;

class UsuarioRepository implements IUsuarioRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait, QueryBuilderTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function create(array $data)
    {
        if (empty($data) || !isset($data['email'])) {
            return null;
        }

        try {
            $usuario = $this->model->create($data);
            if (is_null($usuario->id)) {
                return null;
            }

            return $usuario;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findAll(array $criteria = [], array $orderBy = [], array $orWhereCriteria = [])
    {
        $cacheKey = $this->makeCacheKey($criteria, $orderBy, $orWhereCriteria);

        return $this->getFromCacheOrFetch(
            $cacheKey,
            fn() => $this->buildQuery($criteria, $orderBy, $orWhereCriteria)->get(),
            1800
        );
    }

    public function update(int $id, array $data)
    {
        $usuario = $this->model->find($id);

        if (is_null($usuario)) {
            return null;
        }

        try {
            $usuario->update($data);
            return $this->findById($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function delete(int $id)
    {
        $usuario = $this->model->find($id);

        if (is_null($usuario)) {
            return null;
        }

        try {
            $usuario->delete();
            return true;
        } catch (\Exception $e) {
            return null;
        }

        return false;
    }
}
