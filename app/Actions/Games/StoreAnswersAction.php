<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Models\User;
use App\Services\MailService;

class StoreAnswersAction
{
    protected $mailService;

    public function __construct()
    {
        $this->mailService = app(MailService::class);
    }

    public function handle(Game $game, User $user, array $data)
    {
        // Find the game_user entry for the current user and the specified game
        $gameUser = GameUser::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->where('status', '!=', 'Host')
            ->first();

        if (! $gameUser) {
            return ['status' => 'error', 'message' => 'User not found for this game'];
        }

        // Loop through the answers and create records in the database
        foreach ($data['answers'] as $id => $answerValue) {
            GameUserQuestions::where('id', $id)->update(['answer' => $answerValue]);
        }

        $status = $gameUser->status;

        // if this is the first time filling out the form, let the host know this person completed the form
        if ($gameUser->status !== 'All Questions Answered') {

            // Update the player's status in the pivot table
            $status = 'Questions Answered';
            $game->players()->updateExistingPivot($user->id, ['status' => $status]);

            // check to see how many people we are waiting for
            $game->updateStatusIfReady();

            $result = $this->mailService->sendPlayerAnsweredQuestions($user, $game, $noAnswers);
        }

        return ['status' => 'success', 'message' => $status];
    }
}
