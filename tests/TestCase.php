<?php

namespace Tests;

use App\Jobs\DeleteUserFromMailchimpJob;
use App\Jobs\SyncUserWithMailchimpJob;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Bus;

abstract class TestCase extends BaseTestCase
{
    private static ?string $testAppKey = null;

    /**
     * Tests use a throwaway encryption key generated once per run, so no
     * real key has to live in the repository or be readable from .env.
     */
    public function createApplication(): Application
    {
        $app = parent::createApplication();

        self::$testAppKey ??= 'base64:'.base64_encode(random_bytes(32));
        $app['config']->set('app.key', self::$testAppKey);

        return $app;
    }

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
