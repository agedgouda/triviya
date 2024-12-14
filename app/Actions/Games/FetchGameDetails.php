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
            ->where('status','like', '%invitation%')
            ->first();

        if ($hasQuestions) {
            return [
                'redirect' => route('games.showQuestions', [
                    'game' => $game->id,
                    'user' => $userId,
                ]),
            ];
        }

        // Fetch game details
        $players = $game->players;
        $host = $game->host;
        $questions = $game->questions;
        $game = $game->load(['mode']);

        return [
            'game' => $game,
            'players' => $players,
            'host' => $host,
            'questions' => $questions,
        ];
    }
}
