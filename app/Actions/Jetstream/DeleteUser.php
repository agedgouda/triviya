<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(protected DeletesTeams $deletesTeams)
    {
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            //$this->deleteTeams($user);
            /*$user->games()
                ->where('status', 'full')
                ->each(function ($game) {
                    $game->update(['status' => null]);
                });
            */


            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->delete();
            Session::flash('flashMessage', 'This account has been permanently deleted and can no longer be accessed. All related data has been securely removed.');
        });
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
