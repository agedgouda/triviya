<?php

namespace App\Observers;

use App\Jobs\SyncUserWithMailchimpJob;
use App\Jobs\DeleteUserFromMailchimpJob;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {

        SyncUserWithMailchimpJob::dispatch($user->id);

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {

        $oldEmail = $user->getOriginal('email'); // get previous email
        SyncUserWithMailchimpJob::dispatch($user->id, $oldEmail);

    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user)
    {
         DeleteUserFromMailchimpJob::dispatch($user->email);
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
