<?php

namespace App\Actions\Games;

use App\Models\Answer;
use App\Models\Game;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateGameQuestions
{
    public function handle(Game $game)
    {
        // Retrieve all players for the given game
        $players = $game->players;

        // Ensure players exist
        $playerCount = $players->count();
        if ($playerCount === 0) {
            return response()->json(['error' => 'No players in the game'], 400);
        }

        // Retrieve all game_user IDs for the given game
        $gameUserIds = $game->players()->pluck('game_user.id');

        // Delete existing records for the game
        DB::table('game_user_question')->where('game_id', $game->id)->delete();

        // Retrieve all answers that belong to the game users except event questions
        $answers = Answer::whereIn('game_user_id', $gameUserIds)
            ->whereNot('question_id', '9da1a76e-c444-4fa0-8bd6-97f22924d032')
            ->get();

        // Group answers by question_id to ensure uniqueness
        $uniqueAnswers = $answers->unique('question_id');

        if ($uniqueAnswers->isEmpty()) {
            return response()->json(['error' => 'No unique questions available for the game'], 400);
        }

        // Shuffle unique answers for randomness
        $shuffledAnswers = $uniqueAnswers->shuffle();

        // Prepare all questions and answers in memory
        $assignments = [];

        // We'll create a pool of players for fair round distribution
        $playerPool = $players->pluck('id')->toArray();
        $currentIndex = 0;

        foreach ($shuffledAnswers as $answer) {
            // Get the current player ID from the pool
            $playerId = $playerPool[$currentIndex];
            $player = $players->firstWhere('id', $playerId);

            // Move to the next index
            $currentIndex++;

            // If we reached the end of the pool, reshuffle for fairness
            if ($currentIndex >= count($playerPool)) {
                shuffle($playerPool);
                $currentIndex = 0;
            }

            // Retrieve the related question (avoid N+1 with preloaded map in production)
            $question = Question::find($answer->question_id);

            // Format the answer if the question type is 'date'
            if ($question && $question->question_type === 'date') {
                try {
                    $answer->answer = Carbon::createFromFormat('Y-m-d', $answer->answer)->format('M jS, Y');
                } catch (\Exception $e) {
                    // If invalid date, leave original answer
                }
            }

            // Add to assignments array
            $assignments[] = [
                'game_id' => $game->id,
                'player_name' => $player->name,
                'question_text' => $question->question_text ?? '',
                'answer' => $answer->answer,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all assignments in one go
        DB::table('game_user_question')->insert($assignments);

        // Figure out how many event questions to add total
        $questionsToAdd = intdiv(40, $playerCount);

        foreach ($players as $player) {
            // Find how many questions this player already has
            $playerQuestionCount = collect($assignments)->where('player_name', $player->name)->count();

            $numPlayerQuestions = $questionsToAdd - $playerQuestionCount;
            $gameUserId = $game->players()
                ->where('users.id', $player->id)
                ->first()?->pivot->id;

            $answer = Answer::whereHas('gameUser', function ($query) use ($game, $player) {
                $query->where('id', $game->players()->where('users.id', $player->id)->first()?->pivot->id);
            })
                ->where('question_id', '9da1a76e-c444-4fa0-8bd6-97f22924d032')
                ->first();

            for ($i = 0; $i < $numPlayerQuestions; $i++) {
                \Log::info("Player {$player->name} (GameUserID: $gameUserId) needs event question #$i");
                // TODO: Insert event question assignment if needed
            }
        }

        \Log::info("Target questions per player: $questionsToAdd");

        return DB::table('game_user_question')->where('game_id', $game->id)->get();
    }
}
