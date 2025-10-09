<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\GameUserQuestions;
use Inertia\Inertia;

class GameEventController extends Controller
{
    public function startGame(Game $game, ?int $reset = null)
    {
        $response = GameActions::CreateEventQuestionsAction($game, $reset);

        return Inertia::render('Event/Show', [
            'questions' => $response,
            'game' => $game,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function startRound(Game $game, int $round)
    {
        $questions = GameActions::GetRoundQuestionsAction($game, $round);

        return Inertia::render('Event/Show', [
            'game' => $game,
            'questions' => $questions,
            'round' => $round,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function startRound2(Game $game, int $round)
    {
        $lastQuestion = $round * 10;
        $firstQuestion = $lastQuestion - 9;
        $questions = GameUserQuestions::where('game_id', $game->id)
            ->where('question_number', '>=', $firstQuestion)
            ->where('question_number', '<=', $lastQuestion)
            ->orderBy('question_number')
            ->get();

        return Inertia::render('Event/Show', [
            'game' => $game,
            'questions' => $questions,
            'round' => $round,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function endRound(Game $game, int $round)
    {
        $response = GameActions::CreateEventAnswerListAction($game, $round);

        return Inertia::render('Event/Show', [
            'answers' => $response,
            'round' => $round,
            'game' => $game,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function endGame(Game $game)
    {

        $game->status = $game->status === 'in progress' ? 'done-bonus' : 'done';
        $game->save();

        return Inertia::render('Event/Show', [
            'game' => $game->load(['host']),
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }
}
