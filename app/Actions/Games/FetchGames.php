<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Pagination\LengthAwarePaginator;

class FetchGames
{
    public function handle(bool $hosted): LengthAwarePaginator
    {
        $userId = auth()->id();

        $query = Game::with(['players' => function ($q) use ($userId) {
            // Only include pivot status for the current user
            $q->where('user_id', $userId);
        }])->withCount(['players']);

        if ($hosted) {
            $query->hostedBy($userId);
        } else {
            $query->attendedBy($userId);
        }

        // Paginate
        $games = $query->paginate(10);

        // Add current user's status to each game for easier front-end consumption
        $games->getCollection()->transform(function ($game) use ($userId) {
            $game->current_user_status = optional(
                $game->players->first()?->pivot
            )->status ?? null;

            // Remove the players collection to avoid sending redundant data
            unset($game->players);

            return $game;
        });

        return $games;
    }
}
