<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Mode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\InvitePlayer;

use Carbon\Carbon;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gamesHosted = Game::withCount([
            'players as total' => function ($query) {
                $query->where('game_user.is_host', false);
            },
            'players as attending' => function ($query) {
                $query->where('game_user.status', 'Attending');
            },
            'players as not_attending' => function ($query) {
                $query->where('game_user.status', 'Can\'t Make It');
            },
        ]) 
        ->whereHas('host', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->paginate(10);

        $games = Game::withCount([
            'players as total' => function ($query) {
                $query->where('game_user.is_host', false);
            },
            'players as attending' => function ($query) {
                $query->where('game_user.status', 'Attending');
            },
            'players as not_attending' => function ($query) {
                $query->where('game_user.status', 'Can\'t Make It');
            },
        ]) 
        ->whereHas('players', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->paginate(10);

        return Inertia::render('Games/Index', [
            'games' => $games,
            'gamesHosted' => $gamesHosted,
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
        $validated = $this->validateGame($request);
    
        try {
            // Use array_intersect_key to only include fillable fields
            $game = Game::create(
                array_intersect_key($validated, array_flip((new Game)->getFillable()))
            );
        
            $game->players()->attach(auth()->id(), [
                'status' => 'host',
                'is_host' => true,
            ]);
            
            return Redirect::route('games.show', $game->id)->with('flash', [
                'message' => 'Game created successfully!',
            ]);
        } catch (\Exception $e) {
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
            'host' => $game->host,
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

        $validated = $this->validateGame($request);

        try {
            $game->update($validated);

            return Redirect::route('games.show', $game->id)->with('flash', [
                'message' => 'Game updated successfully!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating game: ' . $e->getMessage());

            return Redirect::back()->withErrors([
                'message' => 'There was a problem updating the game. Please try again.',
            ]);
        }
    }

    /**
     * Validate the game data.
     */
    private function validateGame(Request $request): array
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'date_time' => 'required|date', // Valid datetime
                'mode_id' => 'required|exists:modes,id', // Ensure mode_id exists in the modes table
                'location' => 'required|string|max:255',
            ]);
        
            // Ensure the 'date_time' field is in the correct format (YYYY-MM-DD HH:MM:SS)
            if (isset($validated['date_time'])) {
                // Convert to MySQL format if necessary
                $validated['date_time'] = Carbon::parse($validated['date_time'])->format('Y-m-d H:i:s');
            }
        
            return $validated;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Throw the exception to be handled by Laravel automatically
            throw $e;
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        // Implement game deletion logic if needed
    }

    public function createUserAndInvite(Request $request, Game $game)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone_number' => $validated['phone_number'],
            ]
        );

        if ($game->players()->where('user_id', $user->id)->exists()) {
            return redirect()->route('games.show', $game->id)->with('error', 'An invitation to '.$validated['email']. ' has already been sent.');
        }

        $message = "Invitation Created";
        $game->players()->attach($user->id, ['status' => 'invitation created']);

        $emailStatus = $this->sendInvite($user, $game);
        if ($emailStatus['status'] === 'success') {
            $message = "Invitation Sent";
        } else {
            $message = "Error sending invitation";
        }

        $game->players()->syncWithoutDetaching([
            $user->id => ['status' => $message]
        ]);

        return redirect()->route('games.show', $game->id)->with('success', $message);
    }

    public function updateAttendance(Game $game, User $user, bool $attending)
    {
        $status = $attending ? 'Ready for Questions' : 'Can\'t Make It';
        return $this->updatePlayerStatus($game, $user, $status);
    }
    

    public function sendQuestions(Game $game, User $user)
    {
        $status = 'Questions Sent';
        return $this->updatePlayerStatus($game, $user, $status);
    } 


    public function resendInvite(Game $game, User $user)
    {
        $emailStatus = $this->sendInvite($user, $game);
    
        $status = $emailStatus['status'] === 'success' ? 'Invitation Resent' : 'Error sending invitation';
        
        $currentStatus = $game->players()
                        ->wherePivot('user_id', $user->id)
                        ->first()?->pivot->status;
    
        if ($currentStatus === $status) {
            // If the status hasn't changed, skip the update
            return response()->json([
                'status' => 'success',
                'message' => $status,
            ], 200);
        }
    
        return $this->updatePlayerStatus($game, $user, $status);
        
    }
    
    protected function updatePlayerStatus(Game $game, User $user, string $status)
    {   
        try {
            if (!$game->players()->where('user_id', $user->id)->exists()) {
                throw new \Exception('User is not associated with the game.');
            }
    
            $updated = $game->players()->updateExistingPivot($user->id, ['status' => $status]);
    
            if (!$updated ) {
                throw new \Exception('Failed to update the player status in the database.');
            }
    
            return response()->json([
                'status' => 'success',
                'message' => $status,
            ], 200);
        } catch (\Throwable $e) {
            \Log::error("Error updating player status: {$e->getMessage()} - Game ID: {$game->id}, User ID: {$user->id}, Status: {$status}");
    
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function sendInvite($user, $game)
    {
        try {
            Mail::to($user->email)->send(new InvitePlayer($user, $game));
            return ['status' => 'success', 'message' => 'Invite email queued successfully.'];
        } catch (\Throwable $e) {
            \Log::error('Failed to queue invite email: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to queue invite email.', 'error' => $e->getMessage()];
        }
    }
}
