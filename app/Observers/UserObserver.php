<?php

namespace App\Observers;

use App\Models\User;
use App\Jobs\SyncMailchimp;
use App\Services\MailchimpService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        SyncMailchimp::dispatch($user->id);
    }


    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $oldEmail = $user->getOriginal('email'); // get previous email
        SyncMailchimp::dispatch($user->id, $oldEmail);
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
