<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Show the prepopulated registration form for a user.
     */
    public function show(Request $request, Game $game = null)
    {

        $redirectTo = request()->input('redirect_to', route('questions.showQuestions', ['game' => $game->id]));

        // Pass an empty object if no user is found
        return Inertia::render('Auth/Register', [
            'game' => $game ?? (object) [], // Pass an empty object if user is null
            'redirect_to' => $redirectTo
        ]);
    }

    /**
     * Handle the registration process.
     */
    public function store(Request $request, Game $game = null)
    {

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => ['required', 'date', 'before_or_equal:' . now()->subYears(13)->format('Y-m-d')],
            'email' => 'required|email|max:255|unique:users,email', // Ensure email is unique
            'password' => 'required|string|min:8|confirmed',
        ], [
            'birthday.before_or_equal' => 'You must be at least 13 to register.',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $request->phone_number,
            'birthday' => $validated['birthday'],
            'password' => Hash::make($validated['password']),
            'email_opt_in' => $request->email_opt_in,
        ]);

        // Log the user in
        auth()->login($user);

        $redirectTo = request()->input('redirect_to') ?: route('games');

        return redirect()->intended($redirectTo)->with('flashMessage', 'Congratulations! You have registered with TriviYa.');
    }
}
