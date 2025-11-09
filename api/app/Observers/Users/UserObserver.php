<?php

namespace App\Observers\Users;

use App\Models\User;
use App\Repositories\Traits\CacheTrait;

class UserObserver
{
    use CacheTrait;

    public function creating(User $user) {}

    public function created(User $user) {}

    public function updating(User $user) {}

    public function updated(User $user)
    {
        $this->removeCachedObject($user);
    }

    public function deleting(User $user) {}

    public function deleted(User $user)
    {
        $this->removeCachedObject($user);
    }

    public function restored(User $user) {}

    public function forceDeleted(User $user)
    {
        $this->removeCachedObject($user);
    }
}
