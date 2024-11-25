<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Mode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_time' => 'required|date', // Valid datetime
            'mode_id' => 'required|exists:modes,id', // Ensure mode_id exists in the modes table
        ]);
    
        try {
            // Create a new Game instance
            $game = Game::create([
                'name' => $validated['name'],
                'date_time' => $validated['date_time'],
                'mode_id' => $validated['mode_id'],
            ]);
    
            // Attach the authenticated user as the host of the game
            $game->players()->attach(auth()->id(), [
                'status' => 'host',
                'is_host' => true,
            ]);
    
            // Return a JSON response with the new game's ID
            return Redirect::route('games.show', $game->id)->with('flash', [
                'message' => 'Game created successfully!',
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating game: ' . $e->getMessage());
    
            return Redirect::back()->withErrors([
                'message' => 'There was a problem creating the game. Please try again.',
            ]);
        }
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
    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_time' => 'required|date', // Valid datetime
            'mode_id' => 'required|exists:modes,id', // Ensure mode_id exists in the modes table
        ]);

        try {

            $game->update($validated);

            // Redirect to the game's show page with a success flash message
            return Redirect::route('games.show', $game->id)->with('flash', [
                'message' => 'Game updated successfully!',
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error updating game: ' . $e->getMessage());

            // Redirect back with an error message
            return Redirect::back()->withErrors([
                'message' => 'There was a problem updating the game. Please try again.',
            ]);
        }

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
