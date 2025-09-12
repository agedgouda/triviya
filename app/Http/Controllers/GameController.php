<?php
namespace App\Http\Controllers;

use App\Actions\Games\FetchGamesAction;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;
use App\Models\Mode;
use App\Http\Requests\GameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use App\Actions\Games;
use Carbon\Carbon;


class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$gamesHosted = GameActions::fetchGames(true);
        $games = GameActions::fetchGames(false);


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
        $validated = $request->validated();

        $response = GameActions::StoreGameAction($validated);

        if($response["status"] === 'success') {

            return Redirect::route('games.show', $response["game"]->id)->with('flash', [
                'status' => $response["status"],
                'message' => 'Game created successfully!',
            ]);
        } else {
            return redirect()->back()->withErrors([
                'status' =>  $response["status"],
                'message' => $response["message"],
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        $userId = auth()->id();
        $gameDetails = app(\App\Actions\Games\FetchGameDetails::class)->handle($game, $userId);

        return Inertia::render('Games/Index', [
            'game' => $gameDetails['game'],
            'players' => $gameDetails['players'],
            'host' => $gameDetails['host'],
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
        $validated = $request->validated();

        $game->update($validated);

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
        $clientTimeZone = $request->input('timeZone', 'UTC');
        $gameData = $game->toArray();
        unset($gameData['id'], $gameData['created_at'], $gameData['updated_at'],$gameData['status']);
        $gameData['name'] .= ' - ' . Carbon::now()->format('m/d/Y');
        $response = GameActions::StoreGameAction($gameData);
        return Redirect::route('games.show', $response["game"]->id)->with('flash', [
            'message' => 'Game created successfully!',
        ]);
    }




    public function storeAnswers(Request $request, Game $game, User $user)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $response = GameActions::storeAnswersAction($game, $user, $validated);

        //$response = array('status' => 'test');

        if($response['status'] === 'error') {
            return redirect()->back()->withErrors([
                'message' => $response["message"],
            ]);
        }

        return redirect()->route('questions.showThankYou', [
            'game' => $game->id,
            'user' => $user->id,
        ]);
    }

    public function removePlayer(Game $game, User $user) {

        $response = GameActions::RemoveUserAndResetQuestions($game, $user);

        return ['status' => $response["status"], 'message' => $response["message"] , 'game_status' => $response["game_status"] ];

    }

    public function createGameQuestions(Game $game)
    {
        $response = GameActions::createGameQuestions($game);
        return ['status' => 'success', 'message' => $response ];
    }

}
