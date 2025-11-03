<?php

namespace App\Observers;

use App\Models\Game;
use Illuminate\Support\Str;

class GameObserver
{
    /**
     * Handle the Game "creating" event.
     *
     * @param  \App\Models\Game  $game
     * @return void
     */
    public function creating(Game $game)
    {
        // Only generate if short_url is empty
        if (empty($game->short_url)) {
            $game->short_url = $this->generateUniqueShortUrl();
        }
    }

    /**
     * Generate a unique short URL slug.
     */
    protected function generateUniqueShortUrl($length = 6)
    {
        do {
            $slug = Str::random($length);
        } while (\App\Models\Game::where('short_url', $slug)->exists());

        return $slug;
    }
}
