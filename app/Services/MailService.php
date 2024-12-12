<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Mail\InvitePlayer;
use Illuminate\Support\Facades\Mail;

class MailService
{

    public function sendInvite(User $user, Game $game)
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
