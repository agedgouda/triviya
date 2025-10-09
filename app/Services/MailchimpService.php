<?php

namespace App\Services;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Log;

class MailchimpService
{
    protected MailChimp $client;

    protected string $listId;

    public function __construct()
    {
        $this->client = new MailChimp(config('services.mailchimp.key'));
        $this->listId = config('services.mailchimp.list_id');
    }

    /**
     * Add or update a subscriber without overwriting existing fields
     */
    public function upsertMergeSubscriber(string $email, array $newMergeFields = []): bool
    {
        $subscriberHash = md5(strtolower($email));

        // Fetch existing subscriber (if any)
        $existing = $this->client->get("lists/{$this->listId}/members/{$subscriberHash}");

        if ($this->client->success() && isset($existing['merge_fields'])) {
            $mergeFields = array_merge($existing['merge_fields'], $newMergeFields);
        } else {
            $mergeFields = $newMergeFields;
        }

        // Upsert subscriber
        $result = $this->client->put("lists/{$this->listId}/members/{$subscriberHash}", [
            'email_address' => $email,
            'status_if_new' => 'subscribed', // or "pending" if double opt-in is enabled
            'merge_fields' => $mergeFields,
        ]);

        if ($this->client->success()) {
            Log::info('Mailchimp upsert successful', [
                'email' => $email,
                'response' => $result,
            ]);

            return true;
        }

        Log::error('Mailchimp merge upsert failed', [
            'email' => $email,
            'error' => $this->client->getLastError(),
            'details' => $result,
        ]);

        return false;
    }

    /**
     * Delete a subscriber by email.
     */
    public function deleteSubscriber(string $email): bool
    {
        $subscriberHash = md5(strtolower($email));

        $result = $this->client->delete("lists/{$this->listId}/members/{$subscriberHash}");

        if ($this->client->success()) {
            Log::info("Mailchimp subscriber deleted: {$email}");

            return true;
        }

        Log::error("Mailchimp delete failed for {$email}: ".$this->client->getLastError(), ['details' => $result]);

        return false;
    }
}
