<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Models\GameUserQuestions;

class StoreAnswerAction
{

    public function handle(Game $game, User $user, array $data)
    {

        GameUserQuestions::where('id', $data['id'])->update(['answer' => $data['answer']]);

        $numberAnswered = GameUserQuestions::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->whereNotNull('answer')
            ->count();

        $totalQuestions = GameUserQuestions::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->count();

        if ($numberAnswered === $totalQuestions && $totalQuestions > 0) {
            $status = 'Quiz Complete';
        } else {
            $status = $numberAnswered . ' ' . ($numberAnswered === 1 ? 'Question' : 'Questions') . ' Answered';
        }

        //$game->players()->updateExistingPivot($user->id, ['status' => $status]);
        $gameUser = GameUser::where('game_id', $game->id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

        // Update status and fire observer
        $gameUser->status = $status;
        $gameUser->save();

        //check to see if there are at least 4 players and they all have completed their quiz
        /*
        if (
            $game->players()->count() >= 4 &&
            $game->players()->where('status', '!=', 'Quiz Complete')->doesntExist()
        ) {
            $game->status = 'start';
            $game->save();
        }
        */
        return ['status' => 'success', 'message' => $status];
    }
}
