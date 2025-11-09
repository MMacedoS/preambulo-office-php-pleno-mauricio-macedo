<?php

namespace App\Repositories\Contracts\Movies;

interface IFilmeRepository
{
    public function create(array $data);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function findAll(array $criteria = [], array $orderBy = [], array $orWhereCriteria = []);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByIdsWithInsufficientStock(array $movieIds): array;
    public function findAvailableByIds(array $movieIds);
    public function decrementQuantity(int $filmeId, int $quantidade = 1): bool;
    public function incrementQuantity(int $filmeId, int $quantidade = 1): bool;
}
