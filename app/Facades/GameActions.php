<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Calls the matching class in App\Actions\Games and runs its handle() method.
 *
 * @method static void AddUserToGameAction(\App\Models\Game $game, \App\Models\User $user)
 * @method static mixed AssignPlayerQuestionsAction(\App\Models\Game $game, \App\Models\User $user)
 * @method static mixed CreateEventAnswerListAction(\App\Models\Game $game, ?int $round = null)
 * @method static mixed CreateEventQuestionsAction(\App\Models\Game $game, ?int $reset = null)
 * @method static mixed CreateGameQuestions(\App\Models\Game $game)
 * @method static mixed CreateUserForGameAction(\App\Models\Game $game, array $data)
 * @method static void DeleteTestGameAction(\App\Models\Game $game)
 * @method static \App\Models\Game DuplicateGameAction(\App\Models\Game $game)
 * @method static array FetchGameDetails(\App\Models\Game $game, string $userId)
 * @method static mixed FetchGames()
 * @method static array FetchQuestionLandingAction(\App\Models\Game $game, ?string $userId, ?string $currentRouteName = null)
 * @method static \App\Models\Game GenerateTestGameAction(int $playerCount)
 * @method static \Illuminate\Support\Collection GetRoundQuestionsAction(\App\Models\Game $game, int $round)
 * @method static mixed GetUserGameQuestionsAction(\App\Models\Game $game, \App\Models\User $user)
 * @method static array HandleUserShowQuestionsAction(\App\Models\Game $game, ?\App\Models\User $user)
 * @method static bool HasPlayerCompletedQuestionsAction(\App\Models\Game $game, \App\Models\User $user)
 * @method static array JoinGameAsGuestAction(\App\Models\Game $game, string $name)
 * @method static mixed RemoveUserAndResetQuestions(\App\Models\Game $game, \App\Models\User $user)
 * @method static mixed StoreAnswerAction(\App\Models\Game $game, \App\Models\User $user, array $data)
 * @method static mixed StoreAnswersAction(\App\Models\Game $game, \App\Models\User $user, array $data)
 * @method static array StoreGameAction(array $data)
 */
class GameActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\GameActions::class;
    }
}
