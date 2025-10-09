<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;

class CreateEventAnswerListAction
{
    public function handle(Game $game, ?int $round = null)
    {
        $answers = GameUserQuestions::where('game_id', $game->id)
            ->when($round, function ($query, $round) {
                return $query->where('question_number', '<=', $round * 10)
                    ->where('question_number', '>', ($round * 10) - 10);
            }, function ($query) {
                return $query->whereNotNull('question_number');
            })
            ->orderBy('question_number')
            ->get();

        // 2. Load ALL answers for this game (including those with null question_number)
        //    so that duplicates are found regardless of question_number.
        $allAnswers = GameUserQuestions::where('game_id', $game->id)->get();

        // 3. Group all answers by a composite key: question_text and answer.
        $grouped = $allAnswers->groupBy(function ($item) {
            return $item->question_text.'|'.$item->answer;
        });

        // 4. Iterate over the primary $answers and update the player_name if duplicates exist.
        foreach ($answers as $answer) {
            // Build the group key for the current answer.
            $key = $answer->question_text.'|'.$answer->answer;

            // Retrieve the group of answers sharing the same question_text and answer.
            $group = $grouped->get($key, collect());

            // Filter out the current record's player_name.
            $otherPlayerNames = $group->pluck('player_name')
                ->filter(function ($name) use ($answer) {
                    return $name !== $answer->player_name;
                });

            // If there are other names, append them to the current answer's player_name.
            if ($otherPlayerNames->isNotEmpty()) {
                $answer->player_name .= ', '.$otherPlayerNames->implode(', ');
            }
        }

        return $answers;
    }
}
