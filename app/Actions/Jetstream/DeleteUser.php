<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(protected DeletesTeams $deletesTeams) {}

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {

            // Delete all games the user hosts
            $user->hostedGames()->each(function ($game) {
                $game->delete();
            });

            // Clean up associated data
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();

            // Delete the user itself
            $user->delete();
        });

        // Set the confirmation cookie
        Cookie::queue(
            'account_deleted_message',
            'This account has been permanently deleted and can no longer be accessed. All related data has been securely removed.',
            1 // expires in 1 minute
        );
    }

    /**
     * Delete the teams and team associations attached to the user.
     */
    protected function deleteTeams(User $user): void
    {
        $user->teams()->detach();

        $user->ownedTeams->each(function (Team $team) {
            $this->deletesTeams->delete($team);
        });
    }
}
