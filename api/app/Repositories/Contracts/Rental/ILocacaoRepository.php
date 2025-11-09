<?php

namespace App\Repositories\Contracts\Rental;

interface ILocacaoRepository
{
    public function attachMoviesToLocacao(string $locacaoId, array $filmes): void;
    public function detachMoviesFromLocacao(string $locacaoId, array $filmes): void;
    public function calculateTotalValue(string $locacaoId): float;
    public function updateLocacaoTotalValue(string $locacaoId): void;
    public function findAll(array $criteria = [], array $orderBy = [], array $orWhereCriteria = []);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
