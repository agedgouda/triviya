<?php

namespace App\Jobs;

use App\Actions\Mailchimp\SyncUserWithMailchimp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncUserWithMailchimpJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public string $userId;
    public ?string $oldEmail;

    public function __construct(string $userId, ?string $oldEmail = null)
    {
        $this->userId = $userId;
        $this->oldEmail = $oldEmail;
    }

    public function handle(SyncUserWithMailchimp $action)
    {
        $user = User::find($this->userId);

        if (!$user) {
            return;
        }

        $action->execute($user, $this->oldEmail);
    }
}
