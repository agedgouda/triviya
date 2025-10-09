<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MailchimpService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncMailchimp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $userId;

    protected ?string $oldEmail;

    /**
     * @param  string|null  $oldEmail  Previous email if this is an update
     */
    public function __construct(string $userId, ?string $oldEmail = null)
    {
        $this->userId = $userId;
        $this->oldEmail = $oldEmail;
    }

    public function handle()
    {
        $user = User::find($this->userId);

        if (! $user) {
            Log::warning("Mailchimp sync skipped: user ID {$this->userId} not found.");

            return;
        }

        $mailchimp = new MailchimpService;

        // If email changed, delete old subscriber first
        if ($this->oldEmail && $this->oldEmail !== $user->email) {
            $mailchimp->deleteSubscriber($this->oldEmail);
        }

        // Prepare birthday in MM/DD format
        $birthday = $user->birthday ? Carbon::parse($user->birthday)->format('m/d') : '';

        // Upsert subscriber
        $success = $mailchimp->upsertMergeSubscriber(
            $user->email,
            [
                'FNAME' => $user->first_name ?? '',
                'LNAME' => $user->last_name ?? '',
                'BIRTHDAY' => $birthday,
                'PHONE' => $user->phone_number ?? '',
            ]
        );

        if (! $success) {
            Log::error("Mailchimp upsert FAILED for {$user->email}");
        }
    }
}
