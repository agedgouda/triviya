<?php

namespace App\Actions\Games;

use App\Models\Answer;
use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class AssignGameQuestionsAction
{
    public function handle(Game $game)
    {
        // Retrieve all players for the given game
        $players = $game->players;

        // Retrieve all game_user IDs for the given game
        $gameUserIds = $game->players()->pluck('game_user.id');

        // Ensure that there are players
        $playerCount = $players->count();
        if ($playerCount === 0) {
            return response()->json(['error' => 'No players in the game'], 400);
        }

        foreach ($players as $player) {
            $shuffledQuestions = $game->questions->shuffle();

            for ($i = 0; $i < 10; $i++) {
                // Insert the question assignment into the database
                DB::table('game_user_question')->insert([
                    'game_id' => $game->id,
                    'user_id' => $player->id,
                    'player_name' => $player->name,
                    'question_text' => $shuffledQuestions[$i]->question_text,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

        }

        $gameQuestions = DB::table('game_user_question')->where('game_id', $game->id)->get();

        /*
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
                */
        return $gameQuestions;
    }
}
