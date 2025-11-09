<?php

namespace App\Observers\Rental;

use App\Models\Rental\LocacaoFilme;
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

    public function created(LocacaoFilme $locacaoFilme): void {}

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
    }

    public function deleting(LocacaoFilme $locacaoFilme): bool
    {
        return true;
    }

    public function deleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
    }

    public function restored(LocacaoFilme $locacaoFilme): void {}

    public function forceDeleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
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
}
