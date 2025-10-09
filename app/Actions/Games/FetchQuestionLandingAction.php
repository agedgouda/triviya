<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Models\User;

class FetchQuestionLandingAction
{
    public static function handle(Game $game, ?string $userId, ?string $currentRouteName = null): array
    {
        $game->load(['host', 'questions', 'players']);

        $user = null;
        $questions = collect();
        $hasGameUser = false;

        if ($userId) {
            $user = User::find($userId);
            $hasGameUser = GameUser::where('user_id', $userId)
                ->where('game_id', $game->id)
                ->exists();

            if ($hasGameUser) {
                $questions = GameUserQuestions::where('user_id', $userId)
                    ->where('game_id', $game->id)
                    ->get();
            }
        }

        return compact('game', 'user', 'questions', 'hasGameUser');
    }
}
