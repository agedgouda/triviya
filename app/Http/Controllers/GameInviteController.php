<?php
namespace App\Http\Controllers;

use App\Actions\Games\FetchGamesAction;

use App\Facades\GameActions;
use App\Models\Game;
use App\Models\User;
use App\Models\Mode;
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


class GameInviteController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
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
}
