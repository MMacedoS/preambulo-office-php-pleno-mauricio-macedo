<?php

namespace App\Observers\Rental;

use App\Models\Rental\LocacaoFilme;
use App\Repositories\Entities\Rental\LocacaoRepository;
use App\Repositories\Traits\CacheTrait;
use App\Traits\ValidationTrait;

class LocacaoFilmeObserver
{
    use ValidationTrait, CacheTrait;

    public function creating(LocacaoFilme $locacaoFilme): bool
    {
        if (!$this->isRequired($locacaoFilme)) {
            return false;
        }

        return true;
    }

    public function created(LocacaoFilme $locacaoFilme): void
    {
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function updating(LocacaoFilme $locacaoFilme): bool
    {
        if (!$this->isRequired($locacaoFilme)) {
            return false;
        }
        return true;
    }

    public function updated(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function deleting(LocacaoFilme $locacaoFilme): bool
    {
        return true;
    }

    public function deleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function restored(LocacaoFilme $locacaoFilme): void
    {
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function forceDeleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function isRequired(LocacaoFilme $model): bool
    {
        return $this->hasRequiredModelAttributes(
            $model,
            [
                'locacao_id',
                'filme_id',
                'quantidade',
                'preco_unitario',
            ]
        );
    }

    private function updateLocacaoTotalValue(LocacaoFilme $locacaoFilme): void
    {
        try {
            $locacaoRepository = app(LocacaoRepository::class);
            $locacaoRepository->updateLocacaoTotalValue($locacaoFilme->locacao_id);
        } catch (\Exception $e) {
        }
    }
}
