<?php

namespace App\Http\Controllers;

use App\Facades\GameActions;
use App\Http\Requests\GameRequest;
use App\Models\Game;
use App\Models\Mode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $maxPlayers = config('game.max_players');

        $games = GameActions::fetchGames();

        return Inertia::render('Games/Index', [
            'games' => $games,
            'routeName' => request()->route()->getName(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modes = Mode::all();

        return Inertia::render('Games/Index', [
            'routeName' => request()->route()->getName(),
            'modes' => $modes,
            'error' => session('error'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GameRequest $request)
    {
        $response = GameActions::StoreGameAction($request->validated());

        if ($response['status'] === 'success') {

            return Redirect::route('games.show', $response['game']->id)->with('flash', [
                'status' => $response['status'],
                'message' => 'Game created successfully!',
            ]);
        } else {
            return redirect()->back()->withErrors([
                'status' => $response['status'],
                'message' => $response['message'],
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {

        $gameDetails = GameActions::FetchGameDetails($game, auth()->id());

        return Inertia::render('Games/Index', [
            'game' => $gameDetails['game'],
            'players' => $gameDetails['players'],
            'host' => $gameDetails['host'],
            'inviteMessage' => setting('invite_message'),
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        $game->load(['players', 'mode']);
        $modes = Mode::all();

        return Inertia::render('Games/Index', [
            'game' => $game,
            'modes' => $modes,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GameRequest $request, Game $game)
    {
        $game->update($request->validated());

        return Redirect::route('games.show', $game->id)->with('flash', [
            'message' => 'Game updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        // Implement game deletion logic if needed
    }

    /**
     * Make a copy of an existing game and invite those users to answer new questions.
     */
    public function duplicate(Game $game, Request $request)
    {
        $newGame = GameActions::DuplicateGameAction($game, $request->input('timeZone', 'UTC'));

        return redirect()->route('games.show', $newGame->id)
            ->with('flash', ['message' => 'Game duplicated successfully!']);
    }

    public function removePlayer(Game $game, User $user)
    {

        $response = GameActions::RemoveUserAndResetQuestions($game, $user);

        return [
            'status' => $response['status'],
            'message' => $response['message'],
            'game' => $response['game'],
        ];

    }

    public function createGameQuestions(Game $game)
    {
        $response = GameActions::createGameQuestions($game);

        return ['status' => 'success', 'message' => $response];
    }
}
