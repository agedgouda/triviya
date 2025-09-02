<?php

namespace App\Actions\Games;

use App\Models\User;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use Carbon\Carbon;

class RemoveUserAndResetQuestions
{
    public function handle(Game $game, User $user) {

        $deleted = GameUser::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->get()
            ->each
            ->delete();

        if ($deleted) {
            $removeUserQuestions = GameUserQuestions::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->delete();

            // Reset all question_numbers to null for this game.
            GameUserQuestions::where('game_id', $game->id)
                ->update(['question_number' => null]);
            return ['status' => 'success', 'message' => 'User removed'];
        }
        else {

            return ['status' => 'error', 'message' => 'No User Found'];
        }
    }
}
