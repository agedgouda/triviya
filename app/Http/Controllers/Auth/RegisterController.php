<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Show the prepopulated registration form for a user.
     */
    public function show($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->password) {
            return redirect()->route('login')->with('error', 'This user has already registered.');
        }

        return Inertia::render('Auth/Register', [
            'user' => $user,
        ]);
    }

    /**
     * Handle the registration process.
     */
    public function store(Request $request, $userId)
    {

        $user = User::findOrFail($userId);

        if ($user->password) {
            return redirect()->route('login')->with('error', 'This user has already registered.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        auth()->login($user);

        return redirect()->route('games')->with('success', 'Registration successful!');
    }
}
