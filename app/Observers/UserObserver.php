<?php

namespace App\Observers;

use App\Jobs\SyncMailchimp;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        if ($user->email_opt_in) {
            SyncMailchimp::dispatch($user->id);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->email_opt_in) {
            $oldEmail = $user->getOriginal('email'); // get previous email
            SyncMailchimp::dispatch($user->id, $oldEmail);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
