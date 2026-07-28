<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Models\Game;
use Inertia\Inertia;

class GameEventController extends Controller
{
    public function startGame(Game $game, ?int $reset = null)
    {
        $response = GameActions::CreateEventQuestionsAction($game, $reset);

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
        if ($game->status === 'in progress') {
            $game->status = 'done-bonus';
        }

        if ($game->status === 'bonus') {
            $game->status = 'done';
        }

        $game->save();

        return Inertia::render('Event/Show', [
            'game' => $game->load(['host']),
            'hasBonusQuestionsAvailable' => $game->hasBonusQuestionsAvailable(),
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }
}
