<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateEventQuestionsAction
{
    public function handle(Game $game) {
        // Retrieve all players for the given game
        $players = $game->players;

        // Retrieve all game_user IDs for the given game
        $gameUserIds = $game->players()->pluck('game_user.id');

        // Ensure that there are players
        $playerCount = $players->count();
        if ($playerCount === 0) {
            return response()->json(['error' => 'No players in the game'], 400);
        }

        //get all the questions that were answered by players
        $gameQuestions = GameUserQuestions::where('game_id',$game->id)->get();

        //Figure out how many event questions to add total
        $questionsToAdd = intdiv(30, $playerCount);
        $playerQuestionCounts = $players->pluck('id')->mapWithKeys(fn($id) => [$id => 0])->toArray();
        $chooseEventQuestions = collect(); // Initialize as an empty collection outside the loop

        foreach ($playerQuestionCounts as $index => $player) {
            $numPlayerQuestions = $questionsToAdd - $player;

            $gameUserId = $game->players()
                ->where('users.id', $index)
                ->first()?->pivot->id;

            $gameQuestions = GameUserQuestions::where('game_id', $game->id)
                ->where('user_id', $index)
                ->inRandomOrder()
                ->limit($numPlayerQuestions)
                ->get()
                ->toBase(); // Convert to base collection

            $chooseEventQuestions = $chooseEventQuestions->concat($gameQuestions);
            \Log::info($chooseEventQuestions);
        }
        //\Log::info($playerQuestionCounts);
        $eventQuestions = $chooseEventQuestions->shuffle();
        return $eventQuestions;
    }
}
