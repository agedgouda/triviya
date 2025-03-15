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
use App\Models\GameUserQuestions;
use App\Http\Requests\GameRequest;
use App\Http\Requests\InvitePlayerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\InvitePlayer;
use App\Services\MailService;
use App\Actions\Games;
use Carbon\Carbon;


class GameController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
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
    public function store(GameRequest $request)
    {
        $validated = $request->validated();

        $response = GameActions::storeGame($validated);

        if($response["status"] === 'success') {
            return Redirect::route('games.show', $response["game"]->id)->with('flash', [
                'message' => 'Game created successfully!',
            ]);
        } else {
            return redirect()->back()->withErrors([
                'message' => $response["message"],
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
                    'name' => $player->name,
                    'profile_photo_url' => $player->profile_photo_url,
                    'email' => $player->email,
                    'status' => $player->pivot->status,  // Ensures the 'status' is passed
                    'message' => session('message'),
                ];
            }),
            'host' => $gameDetails['host'],
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
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        // Implement game deletion logic if needed
    }

    public function createUser(InvitePlayerRequest $request, Game $game)
    {
        $validated = $request->validated();
        $results = GameActions::createUserForGameAction($game,$validated);
        return redirect()->route('games.show', $game->id)->with($results['status'], $results['message']);

    }

    public function createUserAndInvite(InvitePlayerRequest $request, Game $game)
    {
        $validated = $request->validated();
        $emailStatus = GameActions::createUserAndInviteAction($game,$validated);
        return redirect()->route('games.show', $game->id)->with($emailStatus['status'], $emailStatus['message']);
    }

    public function sendInvitations(Game $game)
    {

        //first assign the questions to the players
        $question = GameActions::AssignGameQuestionsAction($game);

        //then send the invitations
        foreach ($game->players as $player ) {
            $result = $this->mailService->sendInvite($player, $game);
            $status = $result['status'] === 'success' ? 'Invitation Sent' : 'Error sending invitation';
            $game->players()->updateExistingPivot($player->id, ['status' => $status]);
       }

        return ['status' => 'success', 'message' => $game->players ];
    }

    public function resendInvite(Game $game, User $user)
    {
        $result = $this->mailService->sendInvite($user, $game);
        $status = $result['status'] === 'success' ? 'Invitation Resent' : 'Error sending invitation';
        $game->players()->updateExistingPivot($user->id, ['status' => $status]);
        return ['status' => 'success', 'message' => $status ];
    }


    public function updateAttendance(Game $game, User $user, bool $attending)
    {
        if (!$game->players()->where('user_id', $user->id)->exists()) {
            throw new \Exception('User is not associated with the game.');
        }

        $status = $attending ? 'Questions Sent' : 'Can\'t Make It';
        $game->players()->updateExistingPivot($user->id, ['status' => $status]);

        return [
            'status' => 'success',
            'message' => $status,
        ];
    }

    public function showQuestions(Game $game, User $user, Request $request)
    {
        // Load all necessary relationships for the game
        $game->load(['host', 'questions']);

        // Check if the current user is the host
        $isHost = $game->host->id === auth()->id();

        // Load GameUser with answers and user relationship
        $gameUserQuestions = GameUserQuestions::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->get();
        if (!$gameUserQuestions) {
            // Handle error: Player not found in this game
            abort(404, 'Player not found in this game.');
        }

        $answerCount = $gameUserQuestions->filter(function ($question) {
            return !is_null($question->answer) && $question->answer !== '';
        })->count();

        // If the user has answered all questions and is neither the host nor logged in
        if (
            $answerCount >= 10 &&
            !$isHost &&
            !auth()->id()
        ) {
            // Redirect to login or register, based on whether the user has a password
            if ($user->password) {
                return redirect()->route('login.prepopulated', [
                    'game' => $game->id,
                    'user' => $user->id,
                    'redirect_to' => route('questions.showQuestions', ['game' => $game->id, 'user' => $user->id]),
                ]);
            } else {
                session()->flash('message', 'Register to save your answers.');
                return redirect()->route('register.prepopulated', [
                    'game' => $game->id,
                    'user' => $user->id,
                    'redirect_to' => route('games.show', ['game' => $game->id]),
                ]);
            }
        }

        // Determine the correct page to render
        $page = $request->route()->getName() === 'questions.showQuestions' ? 'Questionnaire/Show' : 'Games/Index';


        return Inertia::render($page, [
            'game' => $game,
            'questions' => $gameUserQuestions,
            'user' => $user,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }


    public function showThankYou(Game $game, User $user) {
        return Inertia::render('Questionnaire/Show' , [
            'game' => $game->load(['host']),
            'user' => $user,
            'routeName' => request()->route()->getName(),
        ]);
    }


    public function storeAnswers(Request $request, Game $game, User $user)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $response = GameActions::storeAnswersAction($game, $user, $validated);

        //$response = array('status' => 'test');

        if($response['status'] === 'error') {
            return redirect()->back()->withErrors([
                'message' => $response["message"],
            ]);
        }

        return redirect()->route('questions.showThankYou', [
            'game' => $game->id,
            'user' => $user->id,
        ]);
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
        ->with(['answers' => function ($query) use ($game) {
            $query->whereHas('gameUser', function ($subQuery) use ($game) {
                $subQuery->where('game_id', $game->id);
            })->with('gameUser.user');
        }])
        ->get();

        return Inertia::render('Games/Index', [
            'questions' => $questions,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);

    }

    public function startGame(Game $game, $reset = null)
    {

        $response = GameActions::CreateEventQuestionsAction($game,$reset);

        return Inertia::render('Games/Index', [
            'questions' => $response,
            'routeName' => request()->route()->getName(),
            'error' => session('error'),
        ]);
    }

    public function createGameQuestions(Game $game)
    {
        $response = GameActions::createGameQuestions($game);
        return ['status' => 'success', 'message' => $response ];
    }

    public function showGameQuestions(Game $game) {

        if (! Gate::allows('view-event-questions', $game)) {
            abort(403);
        }


        $questions = DB::table('game_user_question')->where('game_id', $game->id)->get();
        if(!count($questions)){
            $questions = GameActions::createGameQuestions($game);
        }

        return Inertia::render('Event/Show', [
            'questions' => $questions->shuffle(),
        ]);

    }

}
