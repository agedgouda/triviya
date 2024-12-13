<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Exception;

class StoreGame
{
    public function handle(array $data): array
    {
        try {
            // Wrap everything in a transaction
            return DB::transaction(function () use ($data) {
                // Create the game
                $game = Game::create(
                    array_intersect_key($data, array_flip((new Game)->getFillable()))
                );

                // Attach the authenticated user as the host
                $game->players()->attach(auth()->id(), [
                    'status' => 'host',
                    'is_host' => true,
                ]);

                // Retrieve questions with a record in mode_question where mode_id matches the game's mode_id
                $questions = Question::whereHas('modes', function ($query) use ($game) {
                    $query->where('mode_id', $game->mode_id);
                })->get();

                // Check if no questions exist for the selected mode
                if ($questions->isEmpty()) {
                    throw new Exception('No questions found for the selected mode.');
                }

                // Attach the retrieved questions to the game
                $game->questions()->attach($questions->pluck('id')->toArray());

                return [
                    'status' => 'success',
                    'game' => $game,
                ];
            });
        } catch (Exception $e) {
            // Handle any errors during the transaction
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
