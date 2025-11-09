<?php

namespace App\Observers\Rental;

use App\Models\Rental\Locacao;
use App\Repositories\Traits\CacheTrait;
use App\Traits\ValidationTrait;

class LocacaoObserver
{
    use ValidationTrait, CacheTrait;

    public function creating(Locacao $locacao)
    {
        if (!$this->isRequired($locacao)) {
            return false;
        }
        return true;
    }

    public function created(Locacao $locacao) {}

    public function updating(Locacao $locacao)
    {
        if (!$this->isRequired($locacao)) {
            return false;
        }
        return true;
    }

    public function updated(Locacao $locacao)
    {
        $this->removeCachedObject($locacao);
    }

    public function deleting(Locacao $locacao) {}

    public function deleted(Locacao $locacao)
    {
        $this->removeCachedObject($locacao);
    }

    public function restored(Locacao $locacao) {}

    public function forceDeleted(Locacao $locacao) {}

    public function isRequired(Locacao $model): bool
    {
        return $this->hasRequiredModelAttributes(
            $model,
            [
                'usuario_id',
                'data_inicio',
                'data_devolucao',
                'valor_total',
            ]
        );
    }
}
