<?php

namespace App\Actions\Games;

use App\Facades\GameActions;
use App\Models\Game;
use Carbon\Carbon;

class DuplicateGameAction
{
    /**
     * Duplicate a game and add all players, setting status to "Take Quiz".
     */
    public function handle(Game $game): Game
    {
        // Copy the game attributes
        $gameData = $game->only(['name', 'date_time', 'mode_id', 'location']);
        $gameData['name'] .= ' The Sequel ';
        $gameData['status'] = 'new';

        // Create the new game (host is added automatically via StoreGameAction)
        $response = GameActions::StoreGameAction($gameData);
        $newGame = $response['game'];

        // Get the actual host of the new game
        $hostId = $newGame->host->id;

        // Attach all other players from old game (exclude the new host)
        $otherPlayers = $game->players
            ->reject(fn ($player) => $player->id === $hostId)
            ->mapWithKeys(fn ($player) => [
                $player->id => [
                    'status' => 'Available',
                    'is_host' => $player->pivot->is_host,
                ],
            ])
            ->toArray();

        if (! empty($otherPlayers)) {
            $newGame->players()->attach($otherPlayers);
        }

        $game->status = 'done';
        $game->update();

        return $newGame;
    }
}
