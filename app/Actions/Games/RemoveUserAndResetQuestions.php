<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RemoveUserAndResetQuestions
{
    public function handle(Game $game, User $user)
    {
        return DB::transaction(function () use ($game, $user) {

            $deleted = GameUser::where('game_id', $game->id)
                ->where('user_id', $user->id)
                ->get()
                ->each
                ->delete();

            if ($deleted->isEmpty()) {
                return [
                    'status' => 'error',
                    'message' => 'No User Found',
                    'game_status' => $game->status,
                ];
            }

            // Remove user questions
            GameUserQuestions::where('game_id', $game->id)
                ->where('user_id', $user->id)
                ->delete();

            // Reset all question_numbers for this game
            GameUserQuestions::where('game_id', $game->id)
                ->update(['question_number' => null]);

            // Refresh to get the latest status after observer fired
            $game->refresh();

            return [
                'status' => 'success',
                'message' => 'User removed',
                'game' => $game,
            ];
        });
    }
}
