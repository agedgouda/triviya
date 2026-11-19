<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    /**
     * Hosts, players in the game, and admins can view a game.
     */
    public function view(User $user, Game $game): bool
    {
        return $user->is_admin
            || $game->players()->where('users.id', $user->id)->exists();
    }

    /**
     * Only the host or an admin can change or run a game.
     */
    public function manage(User $user, Game $game): bool
    {
        return $user->is_admin || $this->isHost($user, $game);
    }

    /**
     * Event questions are visible to whoever can run the game.
     */
    public function viewEventQuestions(User $user, Game $game): bool
    {
        return $this->manage($user, $game);
    }

    private function isHost(User $user, Game $game): bool
    {
        return $game->players()
            ->wherePivot('is_host', true)
            ->where('users.id', $user->id)
            ->exists();
    }
}
