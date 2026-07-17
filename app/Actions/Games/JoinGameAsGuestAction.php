<?php

namespace App\Actions\Games;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;

class JoinGameAsGuestAction
{
    public function handle(Game $game, string $name): array
    {
        $name = trim($name);

        if (! $game->hasSpace()) {
            return [
                'status' => 'error',
                'message' => 'This game is full.',
            ];
        }

        $game->loadMissing('players');

        $nameTaken = $game->players->contains(
            fn (User $player) => strcasecmp(trim($player->name), $name) === 0
        );

        if ($nameTaken) {
            return [
                'status' => 'error',
                'message' => 'That name is already taken in this game. Please choose another.',
            ];
        }

        $user = User::create([
            'first_name' => $name,
            'last_name' => null,
            'is_guest' => true,
        ]);

        GameActions::AddUserToGameAction($game, $user);

        return [
            'status' => 'success',
            'user' => $user,
        ];
    }
}
