<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;

class GameService
{
public function getGamesForUser(string $userId)
{
    return Game::whereHas('players', function ($query) use ($userId) {
        $query->where('game_user.user_id', $userId);
    })
    ->withCount([
        'players as total_players' => function ($query) {
            $query->where('game_user.is_host', false); // Exclude hosts
        }
    ])
    ->paginate(10);
}

public function getHostedGamesForUser(string $userId)
{
    return Game::whereHas('host', function ($query) use ($userId) {
        $query->where('game_user.user_id', $userId);
    })
    ->withCount([
        'players as total_players' => function ($query) {
            $query->where('game_user.is_host', false); // Exclude hosts
        }
    ])
    ->paginate(10);
}

    public function createGame(array $data, string $userId): Game
    {
        return DB::transaction(function () use ($data, $userId) {
            $game = Game::create($data);

            $game->players()->attach($userId, [
                'status' => 'host',
                'is_host' => true,
            ]);

            return $game;
        });
    }

    public function getGameDetails(Game $game, $userId)
    {
        // Ensure the game exists and is being retrieved
        $game = $game->load(['players', 'mode', 'host', 'questions']);  // load necessary relationships

        // Check if the game is found
        if (!$game) {
            return [
                'game' => null,  // If game not found, return null for the game
                'players' => [],
                'host' => null,
                'questions' => [],
            ];
        }

        // Ensure players and questions are loaded correctly
        $players = $game->players;
        $host = $game->host;  // assuming you have a relationship for host
        $questions = $game->questions;  // assuming game has questions

        // Return the data properly
        return [
            'game' => $game,
            'players' => $players,
            'host' => $host,
            'questions' => $questions,
        ];
    }

    public function updatePlayerStatus(Game $game, User $user, string $status)
    {
        if (!$game->players()->where('user_id', $user->id)->exists()) {
            throw new \Exception('User is not associated with the game.');
        }

        $game->players()->updateExistingPivot($user->id, ['status' => $status]);

        return [
            'status' => 'success',
            'message' => $status,
        ];
    }

    public function storeAnswers(Game $game, string $userId, array $answers)
    {
        $gameUser = $game->players()->where('user_id', $userId)->first();

        if (!$gameUser) {
            throw new \Exception('User is not a participant in this game.');
        }

        foreach ($answers['answers'] as $questionId => $answerText) {
            Answer::updateOrCreate(
                [
                    'game_user_id' => $gameUser->pivot->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer' => $answerText,
                ]
            );
        }

        $status = count($answers['answers']) < $game->questions->count()
            ? count($answers['answers']) . ' of ' . $game->questions->count() . ' Questions Answered'
            : 'All Questions Answered';

        $game->players()->updateExistingPivot($userId, ['status' => $status]);
    }
}
