<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Services\MailService;

class SendGameInviteAction
{
    protected MailService $mailService;

    public function __construct()
    {
        $this->mailService = app(MailService::class);
    }

    public function handle(Game $game, User $user): array
    {
        $result = $this->mailService->sendInvite($user, $game);

        $status = $result['status'] === 'success' ? 'Invitation Sent' : 'Error sending invitation';

        $game->players()->syncWithoutDetaching([
            $user->id => ['status' => $status],
        ]);

        return [
            'status' => 'success',
            'message' => $status,
        ];
    }
}
