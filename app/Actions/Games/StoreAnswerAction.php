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

        $status = $numberAnswered . ' ' . ($numberAnswered == 1 ? 'question' : 'questions') . ' answered';


        $game->players()->updateExistingPivot($user->id, ['status' => $status]);

        return ['status' => 'success', 'message' => $status];
    }
}
