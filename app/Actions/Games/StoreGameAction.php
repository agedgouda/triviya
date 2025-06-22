<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Facades\GameActions;

use Exception;

class StoreGameAction
{
    public function handle(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                // Create the game
                $game = Game::create(
                    array_intersect_key($data, array_flip((new Game)->getFillable()))
                );

                // Prepare the host user from the currently authenticated user
                $authUser = auth()->user();

                if (!$authUser) {
                    throw new Exception('No authenticated user found.');
                }

                // Use CreateUserAndInviteAction to add the host
                $hostData = [
                    'first_name' => $authUser->first_name,
                    'last_name' => $authUser->last_name,
                    'email' => $authUser->email,
                    'is_host' => true,
                ];

                // Get questions for the game mode
                $questions = Question::whereHas('modes', function ($query) use ($game) {
                    $query->where('mode_id', $game->mode_id);
                })->get();

                if ($questions->isEmpty()) {
                    throw new Exception('No questions found for the selected mode.');
                }

                // Attach questions to the game
                $game->questions()->attach($questions->pluck('id')->toArray());

                $hostResult = GameActions::CreateUserForGameAction($game, $hostData);
                if ($hostResult['status'] !== 'success') {
                    throw new Exception('Failed to add host: ' . $hostResult['message']);
                }

                $player = $hostResult['player'];

                $questionResult = GameActions::AssignPlayerQuestionsAction($game, $player);

                if ($questionResult['status'] !== 'success') {
                    // Rollback: detach the player from the game
                    $game->players()->detach($player->id);

                    return [
                        'status' => 'error',
                        'message' => $questionResult['message'] . ' Rolled back player assignment.',
                    ];
                }

                return [
                    'status' => 'success',
                    'game' => $game,
                ];
            });
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
