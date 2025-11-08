<?php

namespace App\Repositories\Entities;

use App\Models\User;
use App\Repositories\Contracts\IUsuarioRepository;
use App\Repositories\Traits\SingletonTrait;
use App\Repositories\Traits\ServiceTrait;
use App\Repositories\Traits\CacheTrait;

class UsuarioRepository implements IUsuarioRepository
{
    use SingletonTrait, ServiceTrait, CacheTrait;

    public function __construct(protected User $model) {}
    public function create(array $data)
    {
        $usuario = $this->model->create($data);
        $this->setCachedObject($usuario, 3600);
        return $usuario;
    }

    public function findById(int $id)
    {
        return $this->getFromCacheOrFetch(
            $this->model->getTable() . '_' . $id,
            function () use ($id) {
                return ServiceTrait::findById($id);
            },
            3600
        );
    }

    public function findByUuid(string $uuid)
    {
        return $this->getFromCacheOrFetch(
            $this->model->getTable() . '_uuid_' . $uuid,
            function () use ($uuid) {
                return ServiceTrait::findByUuid($uuid);
            },
            3600
        );
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
        $usuario = $this->model->find($id);
        if ($usuario) {
            $usuario->update($data);
            $this->deleteFromCacheById($id);
            $this->removeCachedItem($this->model->getTable() . '_all');
            $this->setCachedObject($usuario, 3600);
            return true;
        }
        return false;
    }

    public function delete(int $id)
    {
        $usuario = $this->model->find($id);
        if ($usuario) {
            $usuario->delete();
            $this->deleteFromCacheById($id);
            $this->removeCachedItem($this->model->getTable() . '_all');
            return true;
        }
        return false;
    }
}
