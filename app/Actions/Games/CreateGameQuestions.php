<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateGameQuestions
{

    public function handle(Game $game) {
        // Retrieve all players for the given game
        $players = $game->players;

        // Retrieve all game_user IDs for the given game
        $gameUserIds = $game->players()->pluck('game_user.id');

        // Delete existing records for the game
        DB::table('game_user_question')->where('game_id', $game->id)->delete();

        // Retrieve all answers that belong to the game users
        $answers = Answer::whereIn('game_user_id', $gameUserIds)->get();

        // Group answers by question_id to ensure uniqueness per question
        $uniqueAnswers = $answers->unique('question_id');

        // Ensure that each player gets the same number of questions
        $playerCount = $players->count();
        if ($playerCount === 0) {
            return response()->json(['error' => 'No players in the game'], 400);
        }

        // Determine the number of questions each player gets
        $answersPerPlayer = intdiv($uniqueAnswers->count(), $playerCount);

        if ($answersPerPlayer === 0) {
            return response()->json(['error' => 'Not enough unique questions for all players'], 400);
        }

        // Shuffle the unique answers for randomness
        $shuffledAnswers = $uniqueAnswers->shuffle();

        // Assign answers evenly to each player and save to the database
        foreach ($players as $player) {
            // Get the corresponding game_user entry for this player
            $gameUserId = $game->players()->where('users.id', $player->id)->first()->pivot->id;

            // Get a slice of answers for this player
            $playerSlice = $shuffledAnswers->splice(0, $answersPerPlayer);

            // Insert these assignments into the database
            foreach ($playerSlice as $answer) {
                $question = Question::find($answer->question_id);

                if($question->question_type === 'date'){
                    $answer->answer = Carbon::createFromFormat('Y-m-d', $answer->answer)->format('M jS, Y');
                }

                DB::table('game_user_question')->insert([
                    'game_id' => $game->id,
                    'player_name' => $player->name,
                    'question_text' => $question->question_text,
                    'answer' => $answer->answer,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $questions = DB::table('game_user_question')->where('game_id', $game->id)->get();
        return $questions;
    }
}
