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
    public function show(Request $request, Game $game = null, User $user = null)
    {

        // If user exists but already has a password, redirect to login
        if ($user->password) {
            return redirect()->route('login')->with('error', 'This user has already registered.');
        }

        $redirectTo = request()->input('redirect_to', route('games'));

        // Pass an empty object if no user is found
        return Inertia::render('Auth/Register', [
            'user' => $user ?? (object) [], // Pass an empty object if user is null
            'game' => $game ?? (object) [], // Pass an empty object if user is null
            'redirect_to' => $redirectTo
        ]);
    }

    /**
     * Handle the registration process.
     */
    public function store(Request $request, Game $game = null, User $user = null)
    {

        if ($user) {
            // Case 1: Update an existing user
            $user = User::findOrFail($user->id);

            if ($user->password) {
                return redirect()->route('login')->with('error', 'This user has already registered.');
            }

            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id, // Unique except current user
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        } else {
            // Case 2: Register a new user
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'birthday' => ['nullable', 'date'],
                'email' => 'required|email|max:255|unique:users,email', // Ensure email is unique
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'birthday' => $validated['birthday'],
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Log the user in
        auth()->login($user);
        $redirectTo = request()->input('redirect_to', route('games'));

        return redirect()->intended($redirectTo)->with('message', 'Congratulations! You have registered with TriviYa and your answers have been saved.');
    }
}
