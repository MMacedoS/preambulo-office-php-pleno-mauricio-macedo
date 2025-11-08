<?php

namespace App\Observers;

use App\Models\films\Filme;
use Illuminate\Support\Facades\Log;

class FilmeObserver
{
    /**
     * Handle the Filme "created" event.
     */
    public function created(Filme $filme): void
    {
        Log::info('Filme criado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle the Filme "updated" event.
     */
    public function updated(Filme $filme): void
    {
        Log::info('Filme atualizado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'mudancas' => $filme->getChanges(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle the Filme "deleted" event.
     */
    public function deleted(Filme $filme): void
    {
        Log::warning('Filme deletado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle the Filme "restored" event.
     */
    public function restored(Filme $filme): void
    {
        Log::info('Filme restaurado', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle the Filme "force deleted" event.
     */
    public function forceDeleted(Filme $filme): void
    {
        Log::warning('Filme deletado permanentemente', [
            'uuid' => $filme->uuid,
            'titulo' => $filme->titulo,
            'timestamp' => now(),
        ]);
    }
}
