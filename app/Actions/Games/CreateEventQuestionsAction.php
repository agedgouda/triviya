<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUserQuestions;
use Illuminate\Support\Facades\DB;

class CreateEventQuestionsAction
{
    /**
     * Main entry point
     */
    public function handle(Game $game, ?int $reset = null)
    {
        return DB::transaction(function () use ($game, $reset) {
            if ($reset === 1) {
                return $this->resetQuestions($game);
            }

            if ($reset === -1) {
                return $this->bonusRound($game);
            }

            // Use existing event questions if available
            $eventQuestions = $this->getExistingEventQuestions($game);

            // If none exist, generate new ones
            return $eventQuestions->isEmpty()
                ? $this->generateEventQuestions($game)
                : $eventQuestions;
        });
    }

    /**
     * Reset all question_numbers to null for this game.
     */
    protected function resetQuestions(Game $game)
    {
        GameUserQuestions::where('game_id', $game->id)
            ->update(['question_number' => null]);

        return collect();
    }

    /**
     * Handle bonus round.
     */
    protected function bonusRound(Game $game)
    {
        $game->update(['status' => 'bonus']);

        GameUserQuestions::where('game_id', $game->id)
            ->where('question_number', '>', 0)
            ->update(['question_number' => -1]);

        $selected = GameUserQuestions::where('game_id', $game->id)
            ->whereNull('question_number')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return $this->assignQuestionNumbers($selected);
    }

    /**
     * Get existing questions that are not reset (-1).
     */
    protected function getExistingEventQuestions(Game $game)
    {
        return GameUserQuestions::where('game_id', $game->id)
            ->whereNotNull('question_number')
            ->where('question_number', '<>', -1)
            ->orderBy('question_number')
            ->get();
    }

    /**
     * Generate new event questions if none exist.
     */
    protected function generateEventQuestions(Game $game)
    {
        $players = $game->players()->withPivot('id')->get();

        if ($players->isEmpty()) {
            abort(400, 'No players in the game');
        }

        $playerCount = $players->count();
        $questionsPerPlayer = intdiv(30, $playerCount);

        $selected = collect();

        // Pick questions per player
        foreach ($players as $player) {
            $selected = $selected->merge(
                GameUserQuestions::where('game_id', $game->id)
                    ->where('user_id', $player->id)
                    ->inRandomOrder()
                    ->limit($questionsPerPlayer)
                    ->get()
            );
        }

        // Fill remaining if less than 30
        $remaining = 30 - $selected->count();
        if ($remaining > 0) {
            $additional = GameUserQuestions::where('game_id', $game->id)
                ->whereNotIn('id', $selected->pluck('id'))
                ->inRandomOrder()
                ->limit($remaining)
                ->get();

            $selected = $selected->merge($additional);
        }

        return $this->assignQuestionNumbers($selected);
    }

    /**
     * Shuffle a collection of questions, assign sequential question numbers starting at 1.
     */
    protected function assignQuestionNumbers2($questions)
    {
        $grouped = $questions->groupBy('user_id')->map->values();
        $userIds = $grouped->keys()->shuffle()->values();

        $result = collect();

        // Round-robin shuffle
        while ($grouped->flatten()->isNotEmpty()) {
            foreach ($userIds->shuffle() as $uid) {
                if (!empty($grouped[$uid]) && $grouped[$uid]->isNotEmpty()) {
                    $result->push($grouped[$uid]->shift());
                }
            }
        }

        // Assign sequential numbers in bulk
        foreach ($result as $index => $question) {
            $newNumber = $index + 1;
            $question->updateQuietly(['question_number' => $newNumber]);
            $question->question_number = $newNumber;
        }

        return $result;
    }

    protected function assignQuestionNumbers($questions)
    {
        $grouped = $questions->groupBy('user_id')->map(fn($qs) => $qs->values());
        $userIds = $grouped->keys()->all();
        $numPlayers = count($userIds);

        $result = collect();
        $lastUid = null;

        // Loop until all questions are used
        while ($grouped->flatten()->isNotEmpty()) {
            $round = [];
            $availableIds = $userIds;

            while (!empty($availableIds)) {
                // Filter candidates with questions remaining
                $candidates = array_filter($availableIds, fn($id) => isset($grouped[$id]) && $grouped[$id]->isNotEmpty());

                if (empty($candidates)) {
                    break; // nothing left to pick in this round
                }

                // Prefer candidates that are not equal to lastUid
                $safeCandidates = array_filter($candidates, fn($id) => $id !== $lastUid);

                if (!empty($safeCandidates)) {
                    $candidates = $safeCandidates;
                }

                // Pick one randomly
                $uid = $candidates[array_rand($candidates)];
                $round[] = $uid;
                $lastUid = $uid;

                // Remove from availableIds to avoid duplicates in same round
                $availableIds = array_diff($availableIds, [$uid]);
            }

            // Take one question from each user in this round
            foreach ($round as $uid) {
                if (isset($grouped[$uid]) && $grouped[$uid]->isNotEmpty()) {
                    $result->push($grouped[$uid]->shift());
                }
            }
        }

        // Assign sequential question numbers and update DB
        foreach ($result as $index => $question) {
            $number = $index + 1;
            $question->updateQuietly(['question_number' => $number]);
            $question->question_number = $number;
        }

        return $result;
    }

}
