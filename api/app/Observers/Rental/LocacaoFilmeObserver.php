<?php

namespace App\Observers\Rental;

use App\Models\Rental\LocacaoFilme;
use App\Repositories\Traits\CacheTrait;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Log;

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
        Log::info('Locação de Filme criada', [
            'locacao_id' => $locacaoFilme->locacao_id,
            'filme_id' => $locacaoFilme->filme_id,
        ]);
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
        Log::info('Locação de Filme atualizada', [
            'locacao_id' => $locacaoFilme->locacao_id,
            'filme_id' => $locacaoFilme->filme_id,
        ]);
    }

    public function deleting(LocacaoFilme $locacaoFilme): bool
    {
        return true;
    }

    public function deleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        Log::warning('Locação de Filme deletada', [
            'locacao_id' => $locacaoFilme->locacao_id,
            'filme_id' => $locacaoFilme->filme_id,
            'timestamp' => now(),
        ]);
    }

    public function restored(LocacaoFilme $locacaoFilme): void
    {
        Log::info('Locação de Filme restaurada', [
            'locacao_id' => $locacaoFilme->locacao_id,
            'filme_id' => $locacaoFilme->filme_id,
            'timestamp' => now(),
        ]);
    }

    public function forceDeleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        Log::warning('Locação de Filme deletada permanentemente', [
            'locacao_id' => $locacaoFilme->locacao_id,
            'filme_id' => $locacaoFilme->filme_id,
            'timestamp' => now(),
        ]);
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
