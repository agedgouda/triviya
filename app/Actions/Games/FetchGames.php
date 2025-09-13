<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Pagination\LengthAwarePaginator;

class FetchGames
{
    public function handle(bool $hosted): LengthAwarePaginator
    {
        $userId = auth()->id();

        // Base query
        $query = Game::withCount('players')
            ->with(['players' => function ($q) use ($userId) {
                // Only load pivot info for the current user
                $q->where('user_id', $userId)
                  ->select('users.id'); // minimal select
            }]);

        // Filter by hosted/attended
        $query = $hosted ? $query->hostedBy($userId) : $query->attendedBy($userId);

        // Paginate
        $games = $query->paginate(10);

        // Add current user's status for frontend
        $games->getCollection()->transform(function ($game) {
            $game->current_user_status = optional($game->players->first()?->pivot)->status ?? null;

            // Remove unnecessary players collection
            unset($game->players);

            return $game;
        });

        return $games;
    }
}
