<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Support\Facades\Gate;

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

        $canSeeEmails = Gate::allows('manage', $game);

        $players = $game->players->map(function ($player) use ($userId, $canSeeEmails) {
            return $this->transformUser($player, $userId, $canSeeEmails);
        });

        $host = $this->transformUser($game->host, $userId, $canSeeEmails);

        if (! $canSeeEmails) {
            $game->players->each->makeHidden('email');
            $game->getRelation('host')->each->makeHidden('email');
        }

        // Add hasSpace flag to the game
        $gameArray = $game->toArray();
        $gameArray['hasSpace'] = $game->hasSpace();

        return [
            'game' => $gameArray,
            'players' => $players,
            'host' => $host,
        ];
    }

    /**
     * Transform a user model into a JSON-friendly array
     */
    private function transformUser($user, string $currentUserId, bool $canSeeEmails): array
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
            'email' => ($canSeeEmails || $isSelf) ? $user->email : null,
            'profile_photo_url' => $profilePhotoUrl,
            'status' => $user->pivot->status ?? null,
            'message' => session('message'),
        ];
    }
}
