<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Models\GameUserQuestions;

class AssignPlayerQuestionsAction
{

    public function handle(Game $game, User $user)
    {

        // Select 10 random questions from the game
        $selectedQuestions = $game->questions->shuffle()->take(10);

        // Prepare bulk insert data for question assignments
        $assignments = [];

        /*
        //For when we add birthday back
        $assignments[] = [
            'game_id' => $game->id,
            'user_id' => $player->id,
            'player_name'   => $player->name,
            'question_text' => 'When were you born (month, date, year)?',
            'question_type' => 'date',
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
        */

        foreach ($selectedQuestions as $question) {
            $assignments[] = [
                'game_id' => $game->id,
                'user_id' => $user->id,
                'player_name'   => $user->name,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        // Bulk insert all question assignments at once
        if (!empty($assignments)) {
            GameUserQuestions::insert($assignments);
            $status = 'success';
            $message = 'questions assigned to user';
        } else {
            $status = 'success';
            $message = 'questions assigned to user';
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }
}
