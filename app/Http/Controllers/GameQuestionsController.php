<?php
namespace App\Http\Controllers;

use App\Actions\Games\FetchGamesAction;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Http\Requests\GameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\InvitePlayer;
use App\Actions\Games;


class GameQuestionsController extends Controller
{

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

    public function showThankYou(Game $game, User $user) {
        return Inertia::render('Questionnaire/Show' , [
            'game' => $game->load(['host']),
            'user' => $user,
            'routeName' => request()->route()->getName(),
        ]);
    }


    public function storeAnswer(Request $request, Game $game, User $user)
    {
        $response = GameActions::storeAnswerAction($game, $user, $request->question);

        return $response;
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

}
