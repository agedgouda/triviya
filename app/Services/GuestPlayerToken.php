<?php

namespace App\Services;

use App\Models\Game;

class GuestPlayerToken
{
    /**
     * A token that proves the holder joined this game as this player. It is
     * stored on the player's device so they can resume on a later visit.
     */
    public static function for(Game $game, string $userId): string
    {
        return hash_hmac('sha256', "guest-player|{$game->id}|{$userId}", (string) config('app.key'));
    }

    public static function verify(Game $game, ?string $userId, ?string $token): bool
    {
        if (! $userId || ! $token) {
            return false;
        }

        return hash_equals(self::for($game, $userId), $token);
    }

    public static function sessionKey(Game $game): string
    {
        return "guest_players.{$game->id}";
    }
}
