<?php

namespace Tests;

use App\Jobs\DeleteUserFromMailchimpJob;
use App\Jobs\SyncUserWithMailchimpJob;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Bus;

abstract class TestCase extends BaseTestCase
{
    /**
     * Tests must never reach the real Mailchimp list, so the jobs that call it
     * are always faked. Other jobs still run normally.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake([
            SyncUserWithMailchimpJob::class,
            DeleteUserFromMailchimpJob::class,
        ]);
    }
}
