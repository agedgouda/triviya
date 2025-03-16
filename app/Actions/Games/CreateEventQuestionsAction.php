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

        //if the user wants to reset the questions, set all question_numbers for the game to null
        if ($reset === 1) {
            GameUserQuestions::where('game_id', $game->id)
                ->update(['question_number' => null]);
                $eventQuestions = collect();
        }
        else {
            // Retrieve all event questions with a question number
            $eventQuestions = GameUserQuestions::where('game_id', $game->id)
                ->whereNotNull('question_number')
                ->orderBy('question_number')
                ->get();
        }

        if ($eventQuestions->isEmpty()) {
            // Retrieve all players for the given game with their pivot data
            $players = $game->players()->withPivot('id')->get();

            // Ensure that there are players
            $playerCount = $players->count();
            if ($playerCount === 0) {
                return response()->json(['error' => 'No players in the game'], 400);
            }

            // Calculate how many event questions to add per player
            $questionsToAddPerPlayer = intdiv(30, $playerCount);

            // Initialize a collection to store selected questions
            $selectedQuestions = collect();

            // Iterate over each player to select questions
            foreach ($players as $player) {
                $playerId = $player->id;

                // Retrieve unanswered questions for the player
                $playerQuestions = GameUserQuestions::where('game_id', $game->id)
                    ->where('user_id', $playerId)
                    ->inRandomOrder()
                    ->limit($questionsToAddPerPlayer)
                    ->get();

                // Merge the player's questions into the selected questions collection
                $selectedQuestions = $selectedQuestions->merge($playerQuestions);
            }

            // If we don't have 30 questions, randomly select additional questions
            if ($selectedQuestions->count() < 30) {
                $questionsNeeded = 30 - $selectedQuestions->count();

                $additionalQuestions = GameUserQuestions::where('game_id', $game->id)
                    ->whereNotIn('id', $selectedQuestions->pluck('id'))
                    ->inRandomOrder()
                    ->limit($questionsNeeded)
                    ->get();

                $selectedQuestions = $selectedQuestions->merge($additionalQuestions);
            }

            // Shuffle the selected questions
            $shuffledQuestions = $selectedQuestions->shuffle();

            // Assign question numbers and update the database
            $shuffledQuestions->each(function ($question, $index) {
                $question->update(['question_number' => $index + 1]);
            });

            // Update the event questions collection
            $eventQuestions = $shuffledQuestions;
        }

        return $eventQuestions;
    }
}

