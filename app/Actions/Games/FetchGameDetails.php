<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUser;

class FetchGameDetails
{
    public function handle(Game $game, string $userId): array
    {
        // Check if the user has questions sent
        $hasQuestions = GameUser::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->where('status', 'like', '%invitation%')
            ->exists();

        if ($hasQuestions) {
            return [
                'redirect' => route('games.showQuestions', [
                    'game' => $game->id,
                    'user' => $userId,
                ]),
            ];
        }

        // Eager load relationships only when necessary
        $game = $game->load([
            'players:id,first_name,last_name,email',
            'host:id,first_name,last_name,email',
            'mode',
        ]);

        return [
            'game' => $game,
            'players' => $game->players,
            'invitees' => $game->invitees,
            'host' => $game->host,
        ];
    }
}
