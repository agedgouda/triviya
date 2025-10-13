<?php

namespace App\Actions\Mailchimp;

use App\Models\User;
use App\Services\MailchimpService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncUserWithMailchimp
{
    /**
     * @param  User  $user
     * @param  string|null  $oldEmail  Previous email if this is an update
     */
    public function execute(User $user, ?string $oldEmail = null): void
    {
        $mailchimp = new MailchimpService;

        // If email changed, delete old subscriber first
        if ($oldEmail && $oldEmail !== $user->email) {
            $mailchimp->deleteSubscriber($oldEmail);
        }

        // Upsert subscriber
        $success = $mailchimp->upsertMergeSubscriber(
            $user->email,
            [
                'FNAME' => $user->first_name ?? '',
                'LNAME' => $user->last_name ?? '',
                'PHONE' => $user->phone_number ?? '',
            ]
        );

        if (! $success) {
            Log::error("Mailchimp upsert FAILED for {$user->email}");
        }
    }
}
