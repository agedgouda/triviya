<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;

class LoginController extends Controller
{
    /**
     * Show the login form with a prepopulated email address.
     */
    public function show(Game $game)
    {
        $redirectTo = request()->input('redirect_to', route('questions.showQuestions', ['game' => $game->id]));


        return inertia('Auth/Login', [
            'game' => $game,
            'redirect_to' => $redirectTo
        ]);
    }

    /**
     * Handle the login submission.
     */
    public function login(Request $request, Game $game)
    {
        $credentials = $request->only('email', 'password');
        $redirectTo = $request->input('redirect_to', route('games.show', ['game' => $game->id]));

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended($redirectTo)->with('message', 'Sample Text');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
