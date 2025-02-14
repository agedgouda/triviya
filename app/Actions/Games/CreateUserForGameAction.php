<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;

class CreateUserForGameAction
{

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
        $isPlaying = $game->players()->where('user_id', $user->id)->exists();

        if ($isPlaying) {
            return [
                'status' => 'error',
                'message' => $data['first_name'] . ' '.$data['last_name'].' is already attached to this game.',
            ];
        }

        $game->players()->attach($user->id, ['status' => 'Pending']);

        return [
            'status' => 'success',
            'message' => 'Pending',
        ];
    }
}
