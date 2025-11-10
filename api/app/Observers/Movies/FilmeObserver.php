<?php

namespace App\Observers\Movies;

use App\Models\Movies\Filme;
use App\Repositories\Traits\CacheTrait;
use App\Traits\ValidationTrait;

class FilmeObserver
{
    use ValidationTrait, CacheTrait;

    public function creating(Filme $filme): bool
    {
        if (!$this->isRequired($filme)) {
            return false;
        }
        return true;
    }

    public function created(Filme $filme): void
    {
        $this->removeCachedObject($filme);
        $this->removeAllCacheByTable('filmes');
    }

    public function updating(Filme $filme): bool
    {
        if (!$this->isRequired($filme)) {
            return false;
        }
        return true;
    }

    public function updated(Filme $filme): void
    {
        $this->removeCachedObject($filme);
        $this->removeAllCacheByTable('filmes');
    }

    public function deleting(Filme $filme): bool
    {
        return true;
    }

    public function deleted(Filme $filme): void
    {
        $this->removeCachedObject($filme);
        $this->removeAllCacheByTable('filmes');
    }

    public function restored(Filme $filme): void {}

    public function forceDeleted(Filme $filme): void
    {
        $this->removeCachedObject($filme);
        $this->removeAllCacheByTable('filmes');
    }

    public function isRequired(Filme $model): bool
    {
        return $this->hasRequiredModelAttributes(
            $model,
            [
                'titulo',
                'sinopse',
                'categoria',
                'ano_lancamento',
                'valor_aluguel',
                'quantidade'
            ]
        );
    }
}
