<?php

namespace App\Observers\Movies;

use App\Models\Movies\Filme;
use Illuminate\Support\Facades\Log;

class FilmeObserver
{
    public function created(Filme $filme): void
    {
        Log::info('Filme criado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }

    public function updated(Filme $filme): void
    {
        Log::info('Filme atualizado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'mudancas' => $filme->getChanges(),
            'timestamp' => now(),
        ]);
    }

    public function deleted(Filme $filme): void
    {
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
}
