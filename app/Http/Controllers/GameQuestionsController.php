<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Http\Requests\JoinGameRequest;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameQuestionsController extends Controller
{
    public function showQuestionLanding(Game $game, Request $request)
    {

        $data = GameActions::FetchQuestionLandingAction($game, $this->resolvePlayerId($request));

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

    public function joinGame(JoinGameRequest $request, Game $game)
    {
        if (! $game->hasSpace() || ! in_array($game->status, ['new', 'ready', 'sequel'])) {
            return redirect()
                ->route('questions.showQuestionLanding', $game->id)
                ->with('error', 'This game is no longer accepting new players.');
        }

        $response = GameActions::JoinGameAsGuestAction($game, $request->validated()['name']);

        if ($response['status'] === 'error') {
            return back()->withErrors(['name' => $response['message']]);
        }

        return redirect()->route('questions.showQuestions', [
            'game' => $game->id,
            'player' => $response['user']->id,
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

        $user = User::find($this->resolvePlayerId($request));

        if (! $user) {
            return redirect()->route('questions.showQuestionLanding', $game->id);
        }

        $data = GameActions::HandleUserShowQuestionsAction($game, $user);

        return Inertia::render('Questionnaire/Show', array_merge($data, [
            'routeName' => $request->route()->getName(),
            'error' => session('error'),
        ]));
    }

    /**
     * Resolve the current player: an authenticated host/user session takes
     * priority, otherwise fall back to the guest id supplied by the client
     * (sourced from localStorage on the frontend).
     */
    private function resolvePlayerId(Request $request): ?string
    {
        return auth()->id() ?? $request->query('player');
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
