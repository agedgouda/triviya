<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Models\GameUserQuestions;


class StoreAnswersAction
{
    public function handle(Game $game, User $user, array $data)
    {
        // Find the game_user entry for the current user and the specified game
        $gameUser = GameUser::where('user_id', $user->id)->where('game_id', $game->id)->first();
        if (!$gameUser) {
            return ['status' => 'error', 'message' => 'User not found for this game'];
        }

        // Loop through the answers and create records in the database
        foreach ($data['answers'] as $id => $answerValue) {
            GameUserQuestions::where('id', $id)->update(['answer' => $answerValue]);
        }

        // Determine the status based on the number of answered questions
        $status = 'Questions Answered';

        // Update the player's status in the pivot table
        $game->players()->updateExistingPivot($user->id, ['status' => $status]);

        $allAnswered = GameUser::where('game_id', $game->id)
            ->where('status', '!=', 'Questions Answered')
            ->where('status', '!=', 'Host')
            ->doesntExist() ? 1 : 0;

        if($allAnswered) {
            Game::where('id',$game->id)->update(['status' => 'ready']);
        } else {
            Game::where('id',$game->id)->update(['status' => 'new']);
        }

        return ['status' => 'success', 'message' => $status];
    }
}
