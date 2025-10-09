<?php

namespace App\Actions\Games;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\GameUserQuestions;

class CreateUserAndInviteAction
{
    protected $mailService;

    public function handle(Game $game, array $data)
    {
        // Step 1: Create or attach player
        $createResult = GameActions::CreateUserForGameAction($game, $data);

        if ($createResult['status'] !== 'success') {
            return [
                'status' => 'error',
                'message' => $createResult['message'],
            ];
        }

        $player = $createResult['player'];

        // Step 2: Assign questions
        $questionResult = GameActions::AssignPlayerQuestionsAction($game, $player);

        if ($questionResult['status'] !== 'success') {
            // Rollback: detach the player from the game
            $game->players()->detach($player->id);

            return [
                'status' => 'error',
                'message' => $questionResult['message'].' Rolled back player assignment.',
            ];
        }

        // Step 3: Send invite
        $inviteResult = GameActions::SendGameInviteAction($game, $player);

        if ($inviteResult['status'] !== 'success') {
            // Rollback: delete assigned questions and detach player
            GameUserQuestions::where('game_id', $game->id)
                ->where('user_id', $player->id)
                ->delete();

            $game->players()->detach($player->id);

            return [
                'status' => 'error',
                'message' => $inviteResult['message'].' Rolled back player and questions.',
            ];
        }

        return [
            'status' => 'success',
            'message' => $inviteResult['message'],
        ];
    }
}
