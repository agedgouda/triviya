<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;
use App\Models\User;

class HasPlayerCompletedQuestionsAction
{
    public function handle(Game $game, User $user): bool
    {
        return GameUserQuestions::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->exists()
            && GameUserQuestions::where('game_id', $game->id)
                ->where('user_id', $user->id)
                ->whereNull('answer')
                ->doesntExist();
    }
}
