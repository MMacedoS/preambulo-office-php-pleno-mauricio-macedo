<?php

namespace App\Observers\Rental;

use App\Models\Rental\LocacaoFilme;
use App\Repositories\Entities\Rental\LocacaoFilmesRepository;
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
        $this->removeAllCacheByTable('locacao_filmes');
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function deleting(LocacaoFilme $locacaoFilme): bool
    {
        $filme = $locacaoFilme->filme;
        if ($filme) {
            $filme->increment('quantidade', $locacaoFilme->quantidade);
            $this->removeCachedObject($filme);
            $this->removeAllCacheByTable('filmes');
        }
        return true;
    }

    public function deleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        $this->removeAllCacheByTable('locacao_filmes');
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function restored(LocacaoFilme $locacaoFilme): void
    {
        $this->updateLocacaoTotalValue($locacaoFilme);
    }

    public function forceDeleted(LocacaoFilme $locacaoFilme): void
    {
        $this->removeCachedObject($locacaoFilme);
        $this->removeAllCacheByTable('locacao_filmes');
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
            $locacaoFilmesRepository = app(LocacaoFilmesRepository::class);
            $locacaoFilmesRepository->updateLocacaoTotalValue($locacaoFilme->locacao_id);
        } catch (\Exception $e) {
        }
    }
}
