<?php

namespace App\Actions\Games;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;

class HandleUserShowQuestionsAction
{
    public static function handle(Game $game, ?User $user): array
    {

        // Load all necessary relationships
        $game->load(['host', 'questions', 'players']);

        if ($user) {

            // Delegate to existing actions
            GameActions::AddUserToGameAction($game, $user);

            $questions = GameActions::GetUserGameQuestionsAction($game, $user);

            return [
                'game' => $game,
                'questions' => $questions,
                'user' => $user,
            ];
        }

        return [
            'game' => $game,
            'user' => null,
            'questions' => null,
        ];
    }
}
