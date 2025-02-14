<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Services\MailService;

use Illuminate\Support\Facades\DB;

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

        // Select 10 random questions from the game
        $selectedQuestions = $game->questions->shuffle()->take(10);

        // Prepare bulk insert data for question assignments
        $now = now();
        $assignments = [];
        foreach ($selectedQuestions as $question) {
            $assignments[] = [
                'game_id'       => $game->id,
                'user_id'       => $player->id,
                'player_name'   => $player->name,
                'question_text' => $question->question_text,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // Bulk insert all question assignments at once
        if (!empty($assignments)) {
            DB::table('game_user_question')->insert($assignments);
        }

        // Attach the user to the game with an "Invitation Created" status
        $game->players()->attach($player->id, ['status' => 'Invitation Created']);

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
