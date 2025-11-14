<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class FetchGames
{
    public function handle()
    {
        $userId = auth()->id();

        $games = Game::query()
            ->join('game_user', 'games.id', '=', 'game_user.game_id')
            ->where('game_user.user_id', $userId)
            ->select([
                'games.id',
                'games.name',
                'games.status',
                'game_user.status as current_user_status',
                'game_user.is_host as is_host',
            ])
            ->selectRaw("
                (
                    SELECT COUNT(*)
                    FROM game_user gu2
                    WHERE gu2.game_id = games.id
                      AND gu2.status = 'completed'
                ) AS completed_count,

                -- Total players in the game
                (
                    SELECT COUNT(*)
                    FROM game_user gu3
                    WHERE gu3.game_id = games.id
                ) AS total_players,

                -- Players who are NOT complete
                (
                    SELECT COUNT(*)
                    FROM game_user gu4
                    WHERE gu4.game_id = games.id
                      AND gu4.status != 'complete'
                ) AS remaining_players
            ")
            ->get();

        return $games;
    }
}
