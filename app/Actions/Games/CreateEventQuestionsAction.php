<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateEventQuestionsAction
{
    public function handle(Game $game, $reset = null)
{
    // Handle reset cases first.
    if ($reset === 1) {
        // Reset all question_numbers to null for this game.
        GameUserQuestions::where('game_id', $game->id)
            ->update(['question_number' => null]);
        $eventQuestions = collect();
    } elseif ($reset === -1) {
        //Bonus round so update games table with proper status
        $game->status = 'bonus';
        $game->save();
        // Mark all questions with a positive question_number as -1.
        GameUserQuestions::where('game_id', $game->id)
            ->where('question_number', '>', 0)
            ->update(['question_number' => -1]);

        // Select 10 new questions (from those that are not assigned yet)
        $selectedQuestions = GameUserQuestions::where('game_id', $game->id)
            ->whereNull('question_number')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $eventQuestions = $this->assignQuestionNumbers($selectedQuestions);
    } else {
        // Use existing questions that have been assigned a number (and not -1).
        $eventQuestions = GameUserQuestions::where('game_id', $game->id)
            ->whereNotNull('question_number')
            ->where('question_number', '<>', -1)
            ->orderBy('question_number')
            ->get();
    }

    // If no event questions exist, then generate them.
    if ($eventQuestions->isEmpty()) {
        $players = $game->players()->withPivot('id')->get();
        $playerCount = $players->count();
        if ($playerCount === 0) {
            return response()->json(['error' => 'No players in the game'], 400);
        }

        // Calculate how many questions per player (assuming a total of 30 needed)
        $questionsToAddPerPlayer = intdiv(30, $playerCount);
        $selectedQuestions = collect();

        // For each player, retrieve a random set of unanswered questions.
        foreach ($players as $player) {
            $playerQuestions = GameUserQuestions::where('game_id', $game->id)
                ->where('user_id', $player->id)
                ->inRandomOrder()
                ->limit($questionsToAddPerPlayer)
                ->get();

            $selectedQuestions = $selectedQuestions->merge($playerQuestions);
        }

        // If we have fewer than 30 questions, pull additional random questions.
        if ($selectedQuestions->count() < 30) {
            $questionsNeeded = 30 - $selectedQuestions->count();
            $additionalQuestions = GameUserQuestions::where('game_id', $game->id)
                ->whereNotIn('id', $selectedQuestions->pluck('id'))
                ->inRandomOrder()
                ->limit($questionsNeeded)
                ->get();
            $selectedQuestions = $selectedQuestions->merge($additionalQuestions);
        }

        $eventQuestions = $this->assignQuestionNumbers($selectedQuestions);
    }

    return $eventQuestions;
}

/**
 * Shuffle a collection of questions, assign sequential question numbers starting at 1,
 * update each record in the database, and return the updated collection.
 */
protected function assignQuestionNumbers($questions)
{
    $shuffled = $questions->shuffle();
    $shuffled->each(function ($question, $index) {
        $newNumber = $index + 1;
        $question->update(['question_number' => $newNumber]);
        // Also update the model instance to reflect the new number.
        $question->question_number = $newNumber;
    });

    return $shuffled;
}

}

