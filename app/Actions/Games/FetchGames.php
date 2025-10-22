<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class FetchGames
{
    public function handle(): LengthAwarePaginator
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
            DB::raw('(SELECT COUNT(*) FROM game_user WHERE game_user.game_id = games.id) as players_count'),
        ]);

        // $query = Game::query()
        // ->join('game_user', 'games.id', '=', 'game_user.game_id')
        // ->where('game_user.user_id', $userId)
        // ->select([
        //     'games.id',
        //     'games.name',
        //     'games.status',
        //     'game_user.status as current_user_status',
        //     'game_user.is_host as is_host',
        //     DB::raw('(SELECT COUNT(*) FROM game_user WHERE game_user.game_id = games.id) as players_count'),
        // ]);

        //$sql = $query->toSql();
        //\Log::info('Game query: ' . $sql, $query->getBindings());

        return $games;
    }
}
