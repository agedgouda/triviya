<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class GamePurchaseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // This is where the closure runs
        return 'Feature is active for this user!';
    }

    public function index2(?string $product = null)
    {
        $user = auth()->user();
        dd(! Feature::for($user)->active('game_purchase_enabled'));
        // Gate feature with Pennant
        if (! Feature::for($user)->active('game_purchase_enabled')) {
            abort(403, 'Game purchasing is currently disabled.');
        }

        return Inertia::render('Purchase/GamePurchasePage', [
            'canPurchase' => true,
            'gameOptions' => [1, 3, 10], // purchase quantities
        ]);
    }

    public function store(Request $request)
    {
        // Handle purchase logic (1, 3, or 10 games)
    }

    public function success()
    {
        // Optional: show confirmation
        return Inertia::render('Purchase/GamePurchaseSuccessPage');
    }
}
