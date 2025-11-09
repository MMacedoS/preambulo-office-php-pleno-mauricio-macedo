<?php

namespace App\Observers\Users;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function creating(User $user)
    {
        Log::info('Usuário criando', [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'timestamp' => now(),
        ]);
    }

    public function created(User $user)
    {
        Log::info('Usuário criado', [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'timestamp' => now(),
        ]);
    }

    public function updating(User $user)
    {
        Log::info('Usuário atualizando', [
            'uuid' => $user->uuid,
            'name' => $user->name,
        ]);
    }

    public function updated(User $user)
    {
        Log::info('Usuário atualizado', [
            'uuid' => $user->uuid,
            'name' => $user->name,
        ]);
    }

    public function deleting(User $user)
    {
        Log::warning('Usuário sendo deletado', [
            'uuid' => $user->uuid,
            'name' => $user->name,
        ]);
    }

    public function deleted(User $user)
    {
        Log::warning('Usuário deletado', [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'timestamp' => now(),
        ]);
    }

    public function restored(User $user)
    {
        Log::info('Usuário restaurado', [
            'uuid' => $user->uuid,
            'name' => $user->name,
        ]);
    }

    public function forceDeleted(User $user)
    {
        Log::warning('Usuário deletado permanentemente', [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'timestamp' => now(),
        ]);
    }
}
