<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Services\MailService;

class CreateUserAndInviteAction
{
    protected $mailService;

    public function __construct()
    {
        $this->mailService = app(MailService::class);
    }

    public function handle(Game $game, array $data)
    {
        // First, create or get the user based on the provided data
        $player = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]
        );

        // Check if the user has already been invited to the game
        $playerExists = $game->players()->where('user_id', $player->id)->exists();

        if ($playerExists) {
            return [
                'status' => 'error',
                'message' => $data['email'] . ' is already playing this game.',
            ];
        }
        // Attach the user to the game with an "Waiting Invitation" status
        $gameUser = GameUser::create([
            'game_id' => $game->id,
            'user_id' => $player->id,
            'status'  => 'Waiting Invitation',
        ]);


        // Select 10 random questions from the game
        $selectedQuestions = $game->questions->shuffle()->take(10);

        // Prepare bulk insert data for question assignments
        $assignments = [];

        /*
        //For when we add birthday back
        $assignments[] = [
            'game_id' => $game->id,
            'user_id' => $player->id,
            'player_name'   => $player->name,
            'question_text' => 'When were you born (month, date, year)?',
            'question_type' => 'date',
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
        */

        foreach ($selectedQuestions as $question) {
            $assignments[] = [
                'game_id' => $game->id,
                'user_id' => $player->id,
                'player_name'   => $player->name,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        // Bulk insert all question assignments at once
        if (!empty($assignments)) {
            GameUserQuestions::insert($assignments);
        }

        $result = $this->mailService->sendInvite($player, $game);

        // Set the status based on the invite sending result
        $status = $result['status'] === 'success' ? 'Invitation Sent' : 'Error sending invitation';

        // Sync the user's invitation status with the game
        $game->players()->syncWithoutDetaching([
            $player->id => ['status' => $status],
        ]);

        return [
            'status' => 'success',
            'message' => $status,
        ];
    }
}
