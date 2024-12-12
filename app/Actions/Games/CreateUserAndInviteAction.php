<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Services\MailService;

class CreateUserAndInviteAction
{
    protected $mailService;

    public function __construct()
    {
        $this->mailService = app(MailService::class);
    }

    public function handle(Game $game, array $data)
    {
        // First, create or get the user based on the provided data
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]
        );

        // Check if the user has already been invited to the game
        $hasInvitation = $game->players()->where('user_id', $user->id)->exists();

        if ($hasInvitation) {
            return [
                'status' => 'error',
                'message' => 'An invitation to ' . $data['email'] . ' has already been sent.',
            ];
        }

        // Attach the user to the game with an "Invitation Created" status
        $game->players()->attach($user->id, ['status' => 'Invitation Created']);

        $result = $this->mailService->sendInvite($user, $game);

        // Set the status based on the invite sending result
        $status = $result['status'] === 'success' ? 'Invitation Sent' : 'Error sending invitation';

        // Sync the user's invitation status with the game
        $game->players()->syncWithoutDetaching([
            $user->id => ['status' => $status],
        ]);

        return [
            'status' => 'success',
            'message' => $status,
        ];
    }
}
