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
        if (
            $locacao->filmes()->exists() &&
            $locacao->isDirty('status') &&
            $locacao->status === 'devolvido'
        ) {
            foreach ($locacao->filmes as $filme) {
                $quantidade = $filme->pivot->quantidade;
                $filme->increment('quantidade', $quantidade);
                $this->removeCachedObject($filme);
                $this->removeAllCacheByTable('filmes');
            }
        }

        $this->removeCachedObject($locacao);
        $this->removeAllCacheByTable('locacaos');
    }

    public function deleting(Locacao $locacao)
    {
        if ($locacao->filmes()->exists()) {
            foreach ($locacao->filmes as $filme) {
                $quantidade = $filme->pivot->quantidade;
                $filme->increment('quantidade', $quantidade);
                $this->removeCachedObject($filme);
                $this->removeAllCacheByTable('filmes');
            }
        }
        return true;
    }

    public function deleted(Locacao $locacao)
    {
        $this->removeCachedObject($locacao);
        $this->removeAllCacheByTable('locacaos');
    }

    public function restored(Locacao $locacao) {}

    public function forceDeleted(Locacao $locacao)
    {
        $this->removeCachedObject($locacao);
        $this->removeAllCacheByTable('locacaos');
    }

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
