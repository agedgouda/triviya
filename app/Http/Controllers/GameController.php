<?php
namespace App\Http\Controllers;

use App\Actions\Games\FetchGamesAction;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;
use App\Models\Mode;
use App\Models\Question;
use App\Models\Answer;
use App\Models\GameUser;
use App\Http\Requests\GameRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\InvitePlayer;
use App\Services\GameService;
use App\Actions\Games;
use App\Services\InviteService;
use Carbon\Carbon;

class GameController extends Controller
{
    protected $gameService;
    protected $inviteService;

    public function __construct(GameService $gameService, InviteService $inviteService)
    {
        $this->gameService = $gameService;
        $this->inviteService = $inviteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gamesHosted = GameActions::fetchGames(true);
        $games = GameActions::fetchGames(false);

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
        $gameDetails =  GameActions::fetchGameDetails($game, auth()->id());

        // Handle redirect if the user has questions sent
        if (isset($gameDetails['redirect'])) {
            return redirect($gameDetails['redirect']);
        }

        // Render the view with the fetched game details
        return Inertia::render('Games/Index', [
            'game' => $gameDetails['game'],
            'players' => $gameDetails['players']->map(function ($player) {
                return [
                    'id' => $player->id,
                    'first_name' => $player->first_name,
                    'last_name' => $player->last_name,
                    'email' => $player->email,
                    'status' => $player->pivot->status,  // Ensures the 'status' is passed
                ];
            }),
            'host' => $gameDetails['host'],
            'questions' => $gameDetails['questions'],
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
    public function update(GameRequest $request, Game $game)
    {
        $validated = $request->validated();

        $game->update($validated);

        return Redirect::route('games.show', $game->id)->with('flash', [
            'message' => 'Game updated successfully!',
        ]);
    }

    /**
     * Validate the game data.
     */
    private function validateGame(Request $request): array
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'date_time' => 'required|date',
                'mode_id' => 'required|exists:modes,id',
                'location' => 'required|string|max:255',
            ]);

            if (isset($validated['date_time'])) {
                $validated['date_time'] = Carbon::parse($validated['date_time'])->format('Y-m-d H:i:s');
            }

            return $validated;
        } catch (\Illuminate\Validation\ValidationException $e) {
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
        ]);

        $emailStatus = $this->inviteService->invitePlayer($game,$validated);

        return redirect()->route('games.show', $game->id)->with($emailStatus['status'], $emailStatus['message']);
    }

    public function updateAttendance(Game $game, User $user, bool $attending)
    {
        $status = $attending ? 'Questions Sent' : 'Can\'t Make It';
        return $this->gameService->updatePlayerStatus($game, $user, $status);
    }

    /**
     * Display the specified resource.
     */
    public function showQuestions(Game $game, User $user)
    {
        // Check if the current user is either the player or the host
        $isHost = false;
        if ($game->host->id === auth()->id()) {
            $isHost = true;
            $game->load('host');
        }

        // Ensure that only the player or host can view the answers
        if (auth()->id() !== $user->id && !$isHost) {
            return redirect()->route('games.show', ['game' => $game->id])
                ->withErrors(['msg' => 'No peeking!']);
        }

        // Retrieve the game user and the associated answers
        $gameUser = $game->players()->where('user_id', $user->id)->first();

        if (!$gameUser) {
            return redirect()->route('games.show', ['game' => $game->id])
                ->withErrors(['msg' => 'Player not found in this game.']);
        }

        // Get the answers related to this player's game entry
        $answers = Answer::where('game_user_id', $gameUser->pivot->id)->get();

        return Inertia::render('Games/Index', [
            'game' => $game,
            'answers' => $answers,
            'questions' => $game->questions,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function storeAnswers(Request $request, Game $game)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);


        // Find the game_user entry for the current user and the specified game
        $gameUser = $game->players()->where('user_id', auth()->id())->first();

        if (!$gameUser) {
            return response()->json([
                'message' => 'You are not a participant in this game.',
            ], 403);
        }

        // Loop through the answers and create records in the database
        foreach ($validated['answers'] as $questionId => $answerText) {
            Answer::updateOrCreate(
                [
                    'game_user_id' => $gameUser->pivot->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer' => $answerText,
                ]
            );
        }


        if(count($validated['answers']) < count($game->questions)) {
            $status = count($validated['answers']).' of '.count($game->questions). ' Questions Answered';
        } else {
            $status = 'Questions Answered';
        }
        $updated = $game->players()->updateExistingPivot(auth()->id(), ['status' => $status]);

        return redirect()->route('games.show', $game->id);
    }

    public function showAnswers(Game $game)
    {
        if (auth()->id() !== $game->host->id) {
            session()->flash('message', 'Cheaters never prosper!');

            return redirect()->back();
        }

        $questions = Question::whereHas('games', function ($query) use ($game) {
            $query->where('games.id', $game->id);
        })
        ->with(['answers.gameUser.user'])
        ->get();

        return Inertia::render('Games/Index', [
            'questions' => $questions,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);

    }
}
