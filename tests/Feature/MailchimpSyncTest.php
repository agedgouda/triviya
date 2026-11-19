<?php

namespace Tests\Feature;

use App\Jobs\DeleteUserFromMailchimpJob;
use App\Jobs\SyncUserWithMailchimpJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class MailchimpSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_user_with_an_email_queues_a_mailchimp_sync(): void
    {
        $user = User::factory()->create();

        Bus::assertDispatched(SyncUserWithMailchimpJob::class, fn (SyncUserWithMailchimpJob $job) => $job->userId === $user->id);
    }

    public function test_guests_without_an_email_are_not_synced(): void
    {
        User::create(['first_name' => 'Guest', 'is_guest' => true]);

        Bus::assertNotDispatched(SyncUserWithMailchimpJob::class);
    }

    public function test_deleting_a_user_queues_a_mailchimp_delete(): void
    {
        $user = User::factory()->create();

        $user->delete();

        Bus::assertDispatched(DeleteUserFromMailchimpJob::class);
    }
}
