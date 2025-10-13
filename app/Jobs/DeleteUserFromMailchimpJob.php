<?php

namespace App\Jobs;

use App\Actions\Mailchimp\DeleteUserFromMailchimp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteUserFromMailchimpJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function handle(DeleteUserFromMailchimp $action)
    {
        $action->executeByEmail($this->email);
    }
}
