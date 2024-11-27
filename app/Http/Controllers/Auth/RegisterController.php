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
    public function show($userId = null)
    {
        $user = null;
    
        if ($userId) {
            // Use find() to prevent the 404 exception from being thrown
            $user = User::find($userId);
    
            // If no user is found, throw a 422 error
            if (!$user) {
                abort(422, 'No user found with the given ID.');
            }
    
            // If user exists but already has a password, redirect to login
            if ($user->password) {
                return redirect()->route('login')->with('error', 'This user has already registered.');
            }
        }
    
        // Pass an empty object if no user is found
        return Inertia::render('Auth/Register', [
            'user' => $user ?? (object) [], // Pass an empty object if user is null
        ]);
    }

    /**
     * Handle the registration process.
     */
    public function store(Request $request, $userId = null)
    {
        if ($userId) {
            // Case 1: Update an existing user
            $user = User::findOrFail($userId);
    
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
                'email' => 'required|email|max:255|unique:users,email', // Ensure email is unique
                'password' => 'required|string|min:8|confirmed',
            ]);
    
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        }
    
        // Log the user in
        auth()->login($user);
    
        return redirect()->route('games')->with('success', 'Registration successful!');
    }
}
