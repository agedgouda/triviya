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
     * Login to answer questions for a game
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $isPlayer = $game->players()->where('users.id', $user->id)->exists();

            if ($game->isLocked() && !$isPlayer) {
                // Game is full or in progress, and they’re not in it
                return redirect()->route('games');
            }

            // Otherwise, let them continue where intended
            $redirectTo = $request->input(
                'redirect_to',
                route('questions.showQuestions', ['game' => $game->id])
            );

            return redirect()->intended($redirectTo)->with('message', 'Welcome Back');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Add flash message
        return redirect('/login')
            ->with('flashMessage', 'Logout Complete. Come back and log in anytime to start playing again.');
    }
}
