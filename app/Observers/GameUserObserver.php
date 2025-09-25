<?php

namespace App\Observers;

use App\Models\GameUser;

class GameUserObserver
{

    public function creating(GameUser $gameUser)
    {
        if (!$gameUser->game->hasSpace()) {
            return false; // cancels the attach
        }
    }

    /**
     * Handle the GameUser "created" event.
     */
    public function created(GameUser $gameUser): void
    {
        $gameUser->game->updateFullStatus();
        $gameUser->game?->updateStatusIfReady();
    }

    /**
     * Handle the GameUser "updated" event.
     */
    public function updated(GameUser $gameUser): void
    {
        $gameUser->game?->updateStatusIfReady();
    }

    /**
     * Handle the GameUser "deleted" event.
     */
    public function deleted(GameUser $gameUser): void
    {
        $gameUser->game->updateFullStatus();
        $gameUser->game?->updateStatusIfReady();
    }

    /**
     * Handle the GameUser "restored" event.
     */
    public function restored(GameUser $gameUser): void
    {
        $gameUser->game?->updateStatusIfReady();
    }

    /**
     * Handle the GameUser "force deleted" event.
     */
    public function forceDeleted(GameUser $gameUser): void
    {
        $gameUser->game?->updateStatusIfReady();
    }
}
