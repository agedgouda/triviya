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
        ->get();

        return $games;
    }
}
