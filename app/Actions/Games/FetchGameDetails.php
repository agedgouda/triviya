<?php

namespace App\Actions\Games;

use App\Models\Game;

class FetchGameDetails
{
    public function handle(Game $game, string $userId): array
    {
        if ($game->status === 'done-bonus') {
            $game->status = 'done';
            $game->save();
        }
        $game->load([
            'players:id,first_name,last_name,email,profile_photo_path',
            'host:id,first_name,last_name,email,profile_photo_path',
            'mode',
        ]);

        $players = $game->players->map(function ($player) use ($userId) {
            return $this->transformUser($player, $userId);
        });

        $host = $this->transformUser($game->host, $userId);

        // Add hasSpace flag to the game
        $gameArray = $game->toArray();
        $gameArray['hasSpace'] = $game->hasSpace();

        return [
            'game' => $gameArray,
            'players' => $players,
            'invitees' => $game->invitees,
            'host' => $host,
        ];
    }

    /**
     * Transform a user model into a JSON-friendly array
     */
    private function transformUser($user, string $currentUserId): array
    {
        $isSelf = $user->id === $currentUserId;

        $profilePhotoUrl = $user->profile_photo_path
            ? $user->profile_photo_url
            : 'https://ui-avatars.com/api/?name='.urlencode($user->name).
                '&color='.($isSelf ? 'FFFFFF' : 'A93390').
                '&background='.($isSelf ? 'A93390' : 'FFFFFF');

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_url' => $profilePhotoUrl,
            'status' => $user->pivot->status ?? null,
            'message' => session('message'),
        ];
    }
}
