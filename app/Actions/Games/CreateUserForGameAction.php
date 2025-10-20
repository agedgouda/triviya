<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUserForGameAction
{
    public function handle(Game $game, array $data)
    {
        // Create or get the user
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]
        );

        return DB::transaction(function () use ($game, $user, $data) {

            // Lock the game row for update to prevent race conditions
            $game = Game::where('id', $game->id)->lockForUpdate()->first();

            // Load current players
            $playerIds = $game->players()->pluck('user_id')->toArray();
            $currentCount = count($playerIds);

            // Prevent adding more than 12 players
            if ($currentCount >= 12) {
                return [
                    'status' => 'error',
                    'message' => 'This game already has 12 players.',
                ];
            }

            // Prevent duplicate player
            if (in_array($user->id, $playerIds)) {
                return [
                    'status' => 'error',
                    'message' => $data['first_name'].' '.$data['last_name'].' is already attached to this game.',
                ];
            }

            // Attach the player
            $game->players()->attach($user->id, [
                'status' => 'Available',
                'is_host' => $data['is_host'] ?? false,
            ]);

            // Update game status if this is the 12th player
            if ($currentCount + 1 === 12) {
                $game->update(['status' => 'full']);
            }

            return [
                'status' => 'success',
                'message' => 'Pending',
                'player' => $user,
            ];
        });
    }
}
