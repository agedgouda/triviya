<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Models\Game;
use Inertia\Inertia;

class GameEventController extends Controller
{
    public function startGame(Game $game, ?int $reset = null)
    {
        $user = auth()->user();

        if ($reset === -1 && $game->isHostsFirstGame($user) && ! $user->hasGamesAvailable()) {
            return redirect()->route('games.endGame', $game);
        }

        $response = GameActions::CreateEventQuestionsAction($game, $reset);

        if ($reset === -1 && $game->isHostsFirstGame($user)) {
            $user->useGameCredit();
        }

        return Inertia::render('Event/Show', [
            'questions' => $response,
            'game' => $game,
            'totalRounds' => $game->totalRounds(),
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function startRound(Game $game, int $round, ?bool $back = false)
    {
        GameActions::CreateEventQuestionsAction($game);

        $questions = GameActions::GetRoundQuestionsAction($game, $round);
        $questionNumber = $back ? count($questions) : null;

        return Inertia::render('Event/Show', [
            'game' => $game,
            'questions' => $questions,
            'round' => $round,
            'totalRounds' => $game->totalRounds(),
            'routeName' => request()->route()->getName(),
            'questionNumber' => $questionNumber,
            'error' => session('error'),
        ]);
    }

    public function endRound(Game $game, int $round, ?bool $back = false)
    {
        GameActions::CreateEventQuestionsAction($game);

        $answers = GameActions::CreateEventAnswerListAction($game, $round);
        $questionNumber = $back ? count($answers) : null;

        return Inertia::render('Event/Show', [
            'answers' => $answers,
            'round' => $round,
            'game' => $game,
            'totalRounds' => $game->totalRounds(),
            'routeName' => request()->route()->getName(),
            'questionNumber' => $questionNumber,
            'error' => session('error'),
        ]);
    }

    public function endGame(Game $game)
    {
        GameActions::CreateEventQuestionsAction($game);

        if ($game->status === 'in progress') {
            $game->status = $game->hasBonusQuestionsAvailable() ? 'done-bonus' : 'done';
        }

        if ($game->status === 'bonus') {
            $game->status = 'done';
        }

        $game->save();

        $user = auth()->user();
        $hasBonusQuestionsAvailable = $game->hasBonusQuestionsAvailable();

        return Inertia::render('Event/Show', [
            'game' => $game->load(['host']),
            'hasBonusQuestionsAvailable' => $hasBonusQuestionsAvailable,
            'bonusLockedForPurchase' => $game->isHostsFirstGame($user) && $hasBonusQuestionsAvailable && ! $user->hasGamesAvailable(),
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }
}
