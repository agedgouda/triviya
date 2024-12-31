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
        $answers = Answer::whereIn('game_user_id', $gameUserIds)
            ->whereNot('question_id', '9da1a76e-c444-4fa0-8bd6-97f22924d032')
            ->get();

        // Group answers by question_id to ensure uniqueness
        $uniqueAnswers = $answers->unique('question_id');

        if ($uniqueAnswers->isEmpty()) {
            return response()->json(['error' => 'No unique questions available for the game'], 400);
        }

        // Ensure that there are players
        $playerCount = $players->count();
        if ($playerCount === 0) {
            return response()->json(['error' => 'No players in the game'], 400);
        }

        // Shuffle unique answers for randomness
        $shuffledAnswers = $uniqueAnswers->shuffle();

        // Initialize a counter for each player's question count as a plain PHP array
        $playerQuestionCounts = $players->pluck('id')->mapWithKeys(fn($id) => [$id => 0])->toArray();
        $totalQuestionCount = 0;

        // Assign answers to players, allowing uneven distribution
        $playerIndex = 0;

        foreach ($shuffledAnswers as $answer) {
            // Get the current player
            $player = $players[$playerIndex];
            $playerIndex = ($playerIndex + 1) % $playerCount; // Move to the next player, wrapping around as needed

            // Retrieve the related question
            $question = Question::find($answer->question_id);

            // Format the answer if the question type is 'date'
            if ($question->question_type === 'date') {
                $answer->answer = Carbon::createFromFormat('Y-m-d', $answer->answer)->format('M jS, Y');
            }

            // Insert the question assignment into the database
            DB::table('game_user_question')->insert([
                'game_id' => $game->id,
                'player_name' => $player->name,
                'question_text' => $question->question_text,
                'answer' => $answer->answer,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Increment the player's question count

            $playerQuestionCounts[$player->id]++;
            $totalQuestionCount++;
        }

        //Figure out how many event questions to add total
        $questionsToAdd = intdiv(40, $playerCount);

        foreach($playerQuestionCounts as $index=>$player) {

            $numPlayerQuestions = $questionsToAdd - $player;
            $choseEventQuestions = [];
            $gameUserId = $game->players()
                ->where('users.id', $index) // Filter by the user's ID
                ->first()?->pivot->id;


            $answer = Answer::whereHas('gameUser', function ($query) use ($game, $index) {
                $query->where('id', $game->players()->where('users.id', $index)->first()?->pivot->id);
            })
            ->where('question_id', '9da1a76e-c444-4fa0-8bd6-97f22924d032') // Filter by the desired question ID
            ->first();
            \Log::info($answer->answer);
            for($i=0; $i < $numPlayerQuestions; $i++){

                $msg = $gameUserId.' needs '.$i;

                \Log::info($msg);
            }

        }

        \Log::info($questionsToAdd);
        $questions = DB::table('game_user_question')->where('game_id', $game->id)->get();
        return $questions;
    }
}



