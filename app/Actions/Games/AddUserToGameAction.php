<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use App\Facades\GameActions;

class AddUserToGameAction
{
    public function handle(Game $game, User $user): void
    {
        $isPlaying = GameUser::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isPlaying) {
            GameUser::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
                'status' => 'Joined Game',
                'is_host' => false,
            ]);

            // Assign questions to the player
            GameActions::AssignPlayerQuestionsAction($game, $user);

            // Reload players relationship
            $game->load('players');
        }
    }
}
