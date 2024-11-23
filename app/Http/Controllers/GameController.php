<?php

namespace App\Http\Controllers;

use App\Models\Game;
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
        ->get();

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        $game->load('players');

        return Inertia::render('Games/Index', [
            'game' => $game,
            'routeName' => request()->route()->getName(),
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
}
