<?php

namespace App\Actions\Games;

use App\Models\Game;

class FetchGames
{
    public function handle(bool $hosted): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Game::withCount([
            'players as total' => function ($query) {
                $query->where('game_user.is_host', false);
            },
            'players as attending' => function ($query) {
                $query->whereIn('game_user.status', ['Questions Answered', 'Questions Sent']);
            },
            'players as not_attending' => function ($query) {
                $query->where('game_user.status', 'Can\'t Make It');
            },
        ]);

        if ($hosted) {
            $query->whereHas('host', function ($query) {
                $query->where('user_id', auth()->id());
            });
        } else {
            $query->whereHas('players', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        return $query->paginate(10);
    }
}
