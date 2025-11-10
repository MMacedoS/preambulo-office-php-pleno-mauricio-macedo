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
    public function calculateDailyPenalty(int $locacaoId): float;
    public function updatePenalty(int $locacaoId): bool;
    public function updateAllExpiredPenalties(): array;
    public function processReturn(int $locacaoId): bool;
    public function getClientActiveRentals(int $clientId);
    public function getClientRentalHistory(int $clientId);
}
