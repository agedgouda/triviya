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
