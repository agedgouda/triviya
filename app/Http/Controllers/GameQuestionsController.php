<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Http\Requests\JoinGameRequest;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use App\Services\GuestPlayerToken;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameQuestionsController extends Controller
{
    public function showQuestionLanding(Game $game, Request $request)
    {
        $playerId = $this->resolvePlayerId($request, $game);

        $data = GameActions::FetchQuestionLandingAction($game, $playerId);

        $routeName = $request->route()->getName();

        if ($data['hasGameUser']) {
            $routeName = GameActions::HasPlayerCompletedQuestionsAction($game, $data['user'])
                ? 'questions.showThankYou'
                : 'questions.showQuestions';
        }

        return Inertia::render('Questionnaire/Show', [
            'game' => $data['game'],
            'questions' => $data['questions'],
            'user' => $data['user'],
            'playerToken' => $this->playerTokenFor($game, $data['user']),
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

        $request->session()->put(GuestPlayerToken::sessionKey($game), $response['user']->id);

        return redirect()->route('questions.showQuestions', [
            'game' => $game->id,
            'player' => $response['user']->id,
            'token' => GuestPlayerToken::for($game, $response['user']->id),
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

        $user = User::find($this->resolvePlayerId($request, $game));

        if (! $user) {
            return redirect()->route('questions.showQuestionLanding', $game->id);
        }

        if (GameActions::HasPlayerCompletedQuestionsAction($game, $user)) {
            return redirect()->route('questions.showThankYou', ['game' => $game->id, 'user' => $user->id]);
        }

        $data = GameActions::HandleUserShowQuestionsAction($game, $user);

        return Inertia::render('Questionnaire/Show', array_merge($data, [
            'playerToken' => $this->playerTokenFor($game, $user),
            'routeName' => $request->route()->getName(),
            'error' => session('error'),
        ]));
    }

    public function showThankYou(Request $request, Game $game, User $user)
    {
        $this->ensureIsCurrentPlayer($request, $game, $user);

        return Inertia::render('Questionnaire/Show', [
            'game' => $game->load(['host']),
            'user' => $user->only(['id', 'first_name', 'last_name', 'name', 'is_guest']),
            'routeName' => $request->route()->getName(),
        ]);
    }

    public function storeAnswer(Request $request, Game $game, User $user)
    {
        $this->ensureIsCurrentPlayer($request, $game, $user);

        return GameActions::StoreAnswerAction($game, $user, $request->question);
    }

    public function storeAnswers(Request $request, Game $game, User $user)
    {
        $this->ensureIsCurrentPlayer($request, $game, $user);

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $response = GameActions::StoreAnswersAction($game, $user, $validated);

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

    /**
     * Resolve the current player: a logged-in user takes priority, then a guest
     * who joined this game in the current session, then a returning guest
     * whose ?player= id is backed by a valid ?token= from their device.
     */
    private function resolvePlayerId(Request $request, Game $game): ?string
    {
        if (auth()->check()) {
            return auth()->id();
        }

        $sessionKey = GuestPlayerToken::sessionKey($game);
        $sessionPlayerId = $request->session()->get($sessionKey);

        if ($sessionPlayerId && $this->isPlayerInGame($game, $sessionPlayerId)) {
            return $sessionPlayerId;
        }

        $playerId = $request->query('player');

        if (GuestPlayerToken::verify($game, $playerId, $request->query('token'))
            && $this->isPlayerInGame($game, $playerId)) {
            $request->session()->put($sessionKey, $playerId);

            return $playerId;
        }

        return null;
    }

    private function ensureIsCurrentPlayer(Request $request, Game $game, User $user): void
    {
        abort_unless($this->resolvePlayerId($request, $game) === $user->id, 403);
    }

    private function isPlayerInGame(Game $game, string $userId): bool
    {
        return GameUser::where('game_id', $game->id)->where('user_id', $userId)->exists();
    }

    private function playerTokenFor(Game $game, ?User $user): ?string
    {
        if (! $user || auth()->check()) {
            return null;
        }

        return GuestPlayerToken::for($game, $user->id);
    }
}
