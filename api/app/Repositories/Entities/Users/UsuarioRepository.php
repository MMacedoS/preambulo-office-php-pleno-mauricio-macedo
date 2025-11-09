<?php

namespace App\Repositories\Entities\Users;

use App\Models\User;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;

class UsuarioRepository implements IUsuarioRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait;

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
        return $this->getFromCacheOrFetch(
            $this->model->getTable() . '_all',
            fn() => $this->model->all(),
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
            return $usuario;
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
            $this->deleteFromCacheById($id);
            $this->removeCachedItem($this->model->getTable() . '_all');
            return true;
        } catch (\Exception $e) {
            return null;
        }

        return false;
    }
}
