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
use App\Actions\Games;
use Carbon\Carbon;


class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$gamesHosted = GameActions::fetchGames(true);
        $games = GameActions::fetchGames(false);


        return Inertia::render('Games/Index', [
            'games' => $games,
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

        $response = GameActions::StoreGameAction($validated);

        if($response["status"] === 'success') {

            return Redirect::route('games.show', $response["game"]->id)->with('flash', [
                'status' => $response["status"],
                'message' => 'Game created successfully!',
            ]);
        } else {
            return redirect()->back()->withErrors([
                'status' =>  $response["status"],
                'message' => $response["message"],
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
public function show(Game $game)
{
    $gameDetails = GameActions::fetchGameDetails($game, auth()->id());

    // Handle redirect if the user has questions sent
    if (isset($gameDetails['redirect'])) {
        return redirect($gameDetails['redirect']);
    }

    // Get current user's ID for comparison
    $currentUserId = auth()->id();

    return Inertia::render('Games/Index', [
        'game' => $gameDetails['game'],
        'players' => $gameDetails['players']->map(function ($player) use ($currentUserId) {
            $isSelf = $player->id === $currentUserId;

            $profilePhotoUrl = $player->profile_photo_path
                ? $player->profile_photo_url
                : 'https://ui-avatars.com/api/?name=' . urlencode($player->name) .
                    '&color=' . ($isSelf ? 'FFFFFF' : 'A93390').
                    '&background=' . ($isSelf ? 'A93390' : 'FFFFFF') ;

            return [
                'id' => $player->id,
                'first_name' => $player->first_name,
                'last_name' => $player->last_name,
                'name' => $player->name,
                'email' => $player->email,
                'profile_photo_url' => $profilePhotoUrl,
                'status' => $player->pivot->status ?? null,
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

    /**
     * Make a copy of an existing game and invite those users to answer new questions.
     */
    public function duplicate(Game $game, Request $request)
    {
        $clientTimeZone = $request->input('timeZone', 'UTC');
        $gameData = $game->toArray();
        unset($gameData['id'], $gameData['created_at'], $gameData['updated_at'],$gameData['status']);
        $gameData['name'] .= ' - ' . Carbon::now()->format('m/d/Y');
        $response = GameActions::StoreGameAction($gameData);
        return Redirect::route('games.show', $response["game"]->id)->with('flash', [
            'message' => 'Game created successfully!',
        ]);
    }



    public function showQuestionLanding(Game $game, Request $request)
    {
        $game->load(['host', 'questions','players']);
        // Check if the current user is the host
        $isHost = $game->host->id ;

        if(auth()->id()) {
            $user = User::find(auth()->id());
            $gameUser = GameUser::where('user_id', auth()->id())
            ->where('game_id', $game->id)
            ->exists();

            if($gameUser) {
                $gameUserQuestions = GameUserQuestions::where('user_id', auth()->id())
                ->where('game_id', $game->id)
                ->get();

                return Inertia::render('Questionnaire/Show' , [
                    'game' => $game,
                    'questions' => $gameUserQuestions,
                    'user' => $user,
                    'routeName' => 'questions.showQuestions',
                    'error' => session('error'),
                ]);
            }

        } else {
            $user = null;
        }

        return Inertia::render('Questionnaire/Show' , [
            'game' => $game,
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

    public function showQuestions(Game $game, Request $request)
    {
        $game->load(['host', 'questions', 'players']);

        $user = auth()->user();
        $isHost = $game->host?->id;

        if ($user) {
            // Add the user to the game and assign questions
            GameActions::AddUserToGameAction($game, $user);

            // Get the current user's questions
            $gameUserQuestions = GameActions::GetUserGameQuestionsAction($game, $user);

            return Inertia::render('Questionnaire/Show', [
                'game' => $game,
                'questions' => $gameUserQuestions,
                'user' => $user,
                'routeName' => $request->route()->getName(),
                'error' => session('error'),
            ]);
        }

        return Inertia::render('Questionnaire/Show', [
            'game' => $game,
            'routeName' => $request->route()->getName(),
            'error' => session('error'),
        ]);
    }



    public function storeAnswer(Request $request, Game $game, User $user)
    {
        $response = GameActions::storeAnswerAction($game, $user, $request->question);

        return $response;
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

    public function removePlayer(Game $game, User $user) {

        $response = GameActions::RemoveUserAndResetQuestions($game, $user);

        return ['status' => $response["status"], 'message' => $response["message"] , 'game_status' => $response["game_status"] ];

    }

    public function createGameQuestions(Game $game)
    {
        $response = GameActions::createGameQuestions($game);
        return ['status' => 'success', 'message' => $response ];
    }

}
