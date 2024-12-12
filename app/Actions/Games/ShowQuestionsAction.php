<?php

namespace App\Actions\Games;

use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;

class ShowQuestionsAction
{
    public function handle(Game $game, User $user)
    {
        // Check if the current user is either the player or the host
        $isHost = false;
        if ($game->host->id === auth()->id()) {
            $isHost = true;
        }

        $gameUser = GameUser::with('answers')->where('user_id', $user->id)->where('game_id', $game->id)->first();
        if (!$gameUser) {
            return redirect()->route('games.show', ['game' => $game->id])
                ->withErrors(['msg' => 'Player not found in this game.']);
        }

        // If the user has answered all of the questions, they need to log in to see their answers
        if (count($game->questions) === count($gameUser->answers) && !$isHost && !auth()->id()) {
            if(!$user->password) {
                session()->flash('message', 'You must login to change your answers.');
                return redirect()->route('login', [
                    'redirect_to' => route('games.showQuestions', ['game' => $game->id, 'user' => $user->id])
                ]);
            }
            else {
                session()->flash('message', 'You must register to change your answers.');
                return redirect()->route('register', [
                    'redirect_to' => route('games.showQuestions', ['game' => $game->id, 'user' => $user->id])
                ]);
            }
        }
//http://trivius.test/questions/9d957e77-e281-4a03-bee0-0d4f8dee1636/9d961073-0a2a-4b6e-b80e-d438291c1f36
        return [
            'game' => $game->load(['host']),
            'answers' => $gameUser->answers,
            'questions' => $game->questions,
        ];
    }
}
