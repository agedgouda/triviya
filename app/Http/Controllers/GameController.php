<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Mode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::withCount(['players' => function ($query) {
            // Exclude the host by filtering the pivot table on 'is_host' field
            $query->where('game_user.is_host', false); // 'game_user' is the pivot table name
        }])
        ->whereHas('host', function ($query) {
            $query->where('user_id', auth()->id()); // Ensure the authenticated user is the host
        })
        ->paginate(10);

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
    public function store(Request $request)
    {
        dd($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        $game->load(['players', 'mode']);

        return Inertia::render('Games/Index', [
            'game' => $game,
            'players' => $game->players->map(function ($player) {
                return [
                    'id' => $player->id,
                    'first_name' => $player->first_name,
                    'last_name' => $player->last_name,
                    'email' => $player->email,
                    'status' => $player->pivot->status, 
                ];
            }),
            'routeName' => request()->route()->getName(),
            'error' => session('error'), 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }

    public function createUserAndInvite(Request $request, Game $game)
    {
        // Validate the request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        // Check if a user with the given email already exists
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone_number' => $validated['phone_number'],
            ]
        );

        // Check if the user is already attached to the game
        if ($game->players()->where('user_id', $user->id)->exists()) {
            // Redirect to the 'games.show' route with an error message
            return redirect()->route('games.show', $game->id)->with('error', 'An invitation to '.$validated['email']. ' has already been sent.');
        }


        // Attach the user to the game in the `game_user` pivot table with the status 'invitation sent'
        $game->players()->attach($user->id, ['status' => 'invitation sent']);

        // Return a JSON response
        return redirect()->route('games.show', $game->id)->with('success', 'Invitation created.');
    }
}
