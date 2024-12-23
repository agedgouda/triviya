<?php

namespace App\Actions\Games;

use App\Models\Game;

class FetchGames
{
    public function handle(bool $hosted): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Game::withCount(['players', 'attendingPlayers']);

        if ($hosted) {
            $query->hostedBy(auth()->id());
        } else {
            $query->attendedBy(auth()->id());
        }

        return $query->paginate(10);
    }
}
