<?php

namespace App\Actions\Games;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\GameUserQuestions;
use App\Models\Mode;

class GenerateTestGameAction
{
    /**
     * Original lines evoking Jimmy Stewart's best-known film roles, written
     * fresh rather than quoted from the films themselves (which remain
     * under copyright) — used as filler answers for generated test players.
     *
     * @var array<int, string>
     */
    private const ANSWERS = [
        'Tried to imagine Bedford Falls without me in it.',
        'Wished on a star and asked for the moon.',
        'Went to Washington to say a word or two.',
        'Talked through the whole filibuster about liberty.',
        'Kept an invisible six-foot rabbit as a best friend.',
        'Watched the neighbors through a rear window all summer.',
        'Climbed a bell tower and could not look down.',
        'Told the Senate exactly what he thought of it.',
        'Declared it a wonderful life after all.',
        'Insisted the rabbit was real, thank you very much.',
        'Ran the family Building and Loan into the ground, on purpose or not.',
        'Chased a mystery through the streets of San Francisco.',
        'Fell for a woman who might not have been who she said.',
        'Stood his ground on the Senate floor until he collapsed.',
        'Argued that no man is a failure who has friends.',
    ];

    public function handle(int $playerCount): Game
    {
        $mode = Mode::first();

        $response = GameActions::StoreGameAction([
            'name' => 'Test Game '.now()->format('M j, g:ia'),
            'mode_id' => $mode->id,
            'is_test' => true,
        ]);

        $game = $response['game'];

        $players = collect([auth()->user()]);

        for ($i = 2; $i <= $playerCount; $i++) {
            $joinResponse = GameActions::JoinGameAsGuestAction($game, "Test Player {$i}");
            $players->push($joinResponse['user']);
        }

        foreach ($players as $player) {
            $questions = GameUserQuestions::where('game_id', $game->id)
                ->where('user_id', $player->id)
                ->get();

            foreach ($questions as $question) {
                GameActions::StoreAnswerAction($game, $player, [
                    'id' => $question->id,
                    'answer' => self::ANSWERS[array_rand(self::ANSWERS)],
                ]);
            }
        }

        return $game->fresh();
    }
}
