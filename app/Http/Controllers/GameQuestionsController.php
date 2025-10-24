<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameQuestionsController extends Controller
{
    public function showQuestionLanding(Game $game, Request $request)
    {

        $data = GameActions::FetchQuestionLandingAction($game, auth()->id());

        $routeName = $data['hasGameUser']
            ? 'questions.showQuestions'
            : $request->route()->getName();

        return Inertia::render('Questionnaire/Show', [
            'game' => $data['game'],
            'questions' => $data['questions'],
            'user' => $data['user'],
            'routeName' => $routeName,
            'error' => session('error'),
        ]);
    }

    public function showQuestions(Game $game, Request $request)
    {

        // Check game status before doing anything
        if (! $game->hasSpace()) {
            $max = config('game.max_players');

            return redirect()
                ->route('games') // or wherever you want them to go
                ->with('flashMessage', "The game you are trying to access has reached capacity. TriviYa has a limit of {$max} players per game.");
        }

        if (! in_array($game->status, ['new', 'ready', 'sequel'])) {
            return redirect()
                ->route('games.show', $game->id) // or wherever you want them to go
                ->with('flashMessage', "You can't change your answers once the game starts.");
        }

        $data = GameActions::HandleUserShowQuestionsAction($game, auth()->user());

        return Inertia::render('Questionnaire/Show', array_merge($data, [
            'routeName' => $request->route()->getName(),
            'error' => session('error'),
        ]));
    }

    public function showThankYou(Game $game, User $user)
    {
        return Inertia::render('Questionnaire/Show', [
            'game' => $game->load(['host']),
            'user' => $user,
            'routeName' => request()->route()->getName(),
        ]);
    }

    public function storeAnswer(Request $request, Game $game, User $user)
    {
        $response = GameActions::storeAnswerAction($game, $user, $request->question);

        return $response;
    }

    public function storeAnswers(Request $request, Game $game, User $user)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $response = GameActions::storeAnswersAction($game, $user, $validated);

        // $response = array('status' => 'test');

        if ($response['status'] === 'error') {
            return redirect()->back()->withErrors([
                'message' => $response['message'],
            ]);
        }

        return redirect()->route('questions.showThankYou', [
            'game' => $game->id,
            'user' => $user->id,
        ]);
    }
}
