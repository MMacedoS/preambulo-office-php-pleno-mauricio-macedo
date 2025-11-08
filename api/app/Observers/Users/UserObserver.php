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

    public function updating(User $user) {}

    public function updated(User $user) {}

    public function deleting(User $user) {}

    public function deleted(User $user) {}

    public function restored(User $user) {}

    public function forceDeleted(User $user) {}
}
