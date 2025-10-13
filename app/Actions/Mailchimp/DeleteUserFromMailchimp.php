<?php

namespace App\Actions\Mailchimp;

use App\Services\MailchimpService;
use Illuminate\Support\Facades\Log;

class DeleteUserFromMailchimp
{
    public function executeByEmail(string $email): void
    {
        $mailchimp = new MailchimpService();

        try {
            $success = $mailchimp->deleteSubscriber($email);

            if (! $success) {
                Log::error("Mailchimp deletion FAILED for {$email}");
            }
        } catch (\Exception $e) {
            Log::error("Mailchimp deletion ERROR for {$email}: " . $e->getMessage());
        }
    }
}
