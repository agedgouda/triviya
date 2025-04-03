<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Mail\InvitePlayer;
use App\Mail\PlayerAnsweredQuestions;
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

    public function sendPlayerAnsweredQuestions(User $user, Game $game, $noAnswers)
    {
        try {
            Mail::to($game->host->email)->send(new PlayerAnsweredQuestions($user, $game, $noAnswers));
            return ['status' => 'success', 'message' => 'Player update mail sent successfully.'];
        } catch (\Throwable $e) {
            \Log::error('Failed to send invite email: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to send player email.', 'error' => $e->getMessage()];
        }
    }
}
