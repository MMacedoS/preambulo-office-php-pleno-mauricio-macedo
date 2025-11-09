<?php

namespace App\Repositories\Contracts\Rental;

interface ILocacaoRepository
{
    public function findAll(array $criteria = [], array $orderBy = [], array $orWhereCriteria = []);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function rentalExpiredsCount(): int;
    public function rentalActiveCount(): int;
    public function rentalCompletedCount(): int;
    public function getExpiredRentals();
    public function calculateDailyPenalty(string $locacaoId): float;
    public function updatePenalty(string $locacaoId): bool;
    public function updateAllExpiredPenalties(): array;
}
