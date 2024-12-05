<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Mail\InvitePlayer;
use Illuminate\Support\Facades\Mail;

class InviteService
{
    public function invitePlayer(Game $game, array $data)
    {

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
            ]
        );

        $hasInvitation = GameUser::where('game_id', $game->id)
            ->where('user_id',  $user->id)
            ->first();

        if ($hasInvitation) {
            return [
                'status' => 'error',
                'message' => 'An invitation to ' . $data['email'] . ' has already been sent.',
            ];
        }

        $game->players()->attach($user->id, ['status' => 'Invitation Created']);

        $result = $this->sendInvite($user, $game);

        $status = $result['status'] === 'success' ? 'Invitation Sent' : 'Error sending invitation';

        $game->players()->syncWithoutDetaching([
            $user->id => ['status' => $status],
        ]);

        return [
            'status' => 'success',
            'message' => $status,
        ];
    }

    public function resendInvite(Game $game, User $user)
    {
        $result = $this->sendInvite($user, $game);

        $status = $result['status'] === 'success' ? 'Invitation Resent' : 'Error sending invitation';

        $game->players()->updateExistingPivot($user->id, ['status' => $status]);

        return $result;
    }

    protected function sendInvite(User $user, Game $game)
    {
        try {
            Mail::to($user->email)->send(new InvitePlayer($user, $game));
            return ['status' => 'success', 'message' => 'Invite email sent successfully.'];
        } catch (\Throwable $e) {
            \Log::error('Failed to send invite email: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to send invite email.', 'error' => $e->getMessage()];
        }
    }
}
