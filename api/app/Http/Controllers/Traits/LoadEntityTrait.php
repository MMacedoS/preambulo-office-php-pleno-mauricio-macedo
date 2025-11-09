<?php

namespace App\Http\Controllers\Traits;

use App\Repositories\Entities\Rental\LocacaoRepository;
use App\Repositories\Entities\Users\UsuarioRepository;

trait LoadEntityTrait
{
    private function isValidUuid(string $uuid): bool
    {
        $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        return preg_match($uuidPattern, $uuid) === 1;
    }

    public function loadUserByUuid(string $uuid): ?int
    {
        if (!$this->isValidUuid($uuid)) {
            return null;
        }

        $usuarioRepository = UsuarioRepository::getInstance();
        $user = $usuarioRepository->findByUuid($uuid);
        return $user?->id;
    }

    public function loadRentalByUuid(string $uuid)
    {
        if (!$this->isValidUuid($uuid)) {
            return null;
        }

        $locacaoRepository = LocacaoRepository::getInstance();
        return $locacaoRepository->findByUuid($uuid);
    }
}
