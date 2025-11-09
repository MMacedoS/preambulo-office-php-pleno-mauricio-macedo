<?php

namespace App\Observers\Movies;

use App\Models\Movies\Filme;
use App\Repositories\Traits\CacheTrait;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Log;

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
        Log::info('Filme criado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
        ]);
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
        Log::info('Filme atualizado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
        ]);
    }

    public function deleting(Filme $filme): bool
    {
        return true;
    }

    public function deleted(Filme $filme): void
    {
        $this->removeCachedObject($filme);
        Log::warning('Filme deletado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }

    public function restored(Filme $filme): void
    {
        Log::info('Filme restaurado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }

    public function forceDeleted(Filme $filme): void
    {
        Log::warning('Filme deletado permanentemente', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
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
