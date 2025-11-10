<?php

namespace App\Repositories\Contracts\Users;

interface IUsuarioRepository
{
    public function create(array $data);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function findAll(array $criteria = [], array $orderBy = [], array $orWhereCriteria = []);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function totalClients(): int;
}
