<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;
use App\Models\User;

class GetUserGameQuestionsAction
{
    public function handle(Game $game, User $user)
    {
        return GameUserQuestions::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->get();
    }
}
