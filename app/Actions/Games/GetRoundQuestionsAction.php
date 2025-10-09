<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;
use Illuminate\Support\Collection;

class GetRoundQuestionsAction
{
    public static function handle(Game $game, int $round): Collection
    {
        $lastQuestion = $round * 10;
        $firstQuestion = $lastQuestion - 9;

        return GameUserQuestions::where('game_id', $game->id)
            ->whereBetween('question_number', [$firstQuestion, $lastQuestion])
            ->orderBy('question_number')
            ->get();
    }
}
