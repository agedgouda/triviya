<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameUserQuestions;
use App\Models\Mode;
use App\Models\Question;
use App\Models\User;
use App\Services\GuestPlayerToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GuestPlayerAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    private function createGameWithQuestions(): Game
    {
        $game = Game::factory()->create([
            'mode_id' => Mode::create(['name' => 'Test Mode'])->id,
            'short_url' => Str::random(8),
        ]);

        $game->questions()->attach(Question::factory()->count(12)->create()->pluck('id'));

        return $game;
    }

    private function joinAsGuest(Game $game, string $name): User
    {
        $this->post(route('questions.join', $game), ['name' => $name]);

        return User::where('first_name', $name)->firstOrFail();
    }

    public function test_a_forged_player_id_without_a_token_is_not_trusted(): void
    {
        $game = $this->createGameWithQuestions();
        $victim = $this->joinAsGuest($game, 'Victim');
        $this->flushSession();

        $this->get(route('questions.showQuestionLanding', ['game' => $game, 'player' => $victim->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('user', null));

        $this->get(route('questions.showQuestions', ['game' => $game, 'player' => $victim->id]))
            ->assertRedirect(route('questions.showQuestionLanding', $game->id));
    }

    public function test_a_token_for_another_player_or_game_is_rejected(): void
    {
        $game = $this->createGameWithQuestions();
        $otherGame = $this->createGameWithQuestions();
        $victim = $this->joinAsGuest($game, 'Victim');
        $this->flushSession();

        $wrongGameToken = GuestPlayerToken::for($otherGame, $victim->id);

        $this->get(route('questions.showQuestionLanding', ['game' => $game, 'player' => $victim->id, 'token' => $wrongGameToken]))
            ->assertInertia(fn (Assert $page) => $page->where('user', null));

        $this->get(route('questions.showQuestionLanding', ['game' => $game, 'player' => $victim->id, 'token' => 'garbage']))
            ->assertInertia(fn (Assert $page) => $page->where('user', null));
    }

    public function test_a_guest_cannot_answer_or_view_as_another_player(): void
    {
        $game = $this->createGameWithQuestions();
        $victim = $this->joinAsGuest($game, 'Victim');
        $victimQuestion = GameUserQuestions::where('game_id', $game->id)->where('user_id', $victim->id)->first();

        $this->flushSession();
        $this->joinAsGuest($game, 'Attacker');

        $this->post(route('questions.playerAnswer', ['game' => $game->id, 'user' => $victim->id]), [
            'question' => ['id' => $victimQuestion->id, 'answer' => 'Hijacked'],
        ])->assertForbidden();

        $this->post(route('questions.playerAnswers', ['game' => $game->id, 'user' => $victim->id]), [
            'answers' => [$victimQuestion->id => 'Hijacked'],
        ])->assertForbidden();

        $this->get(route('questions.showThankYou', ['game' => $game->id, 'user' => $victim->id]))
            ->assertForbidden();

        $this->assertNull($victimQuestion->fresh()->answer);
    }

    public function test_an_anonymous_visitor_cannot_view_a_registered_users_details(): void
    {
        $game = $this->createGameWithQuestions();
        $registered = User::factory()->create();

        $this->get(route('questions.showThankYou', ['game' => $game->id, 'user' => $registered->id]))
            ->assertForbidden();
    }

    public function test_thank_you_page_only_exposes_basic_player_fields(): void
    {
        $game = $this->createGameWithQuestions();
        $player = User::factory()->create(['phone_number' => '555-123-4567']);
        $game->players()->attach($player->id, ['is_host' => false, 'status' => 'Completed']);

        $this->actingAs($player)
            ->get(route('questions.showThankYou', ['game' => $game->id, 'user' => $player->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('user.id', $player->id)
                ->missing('user.email')
                ->missing('user.phone_number')
            );
    }

    public function test_a_logged_in_user_cannot_answer_as_someone_else(): void
    {
        $game = $this->createGameWithQuestions();
        $victim = $this->joinAsGuest($game, 'Victim');
        $victimQuestion = GameUserQuestions::where('game_id', $game->id)->where('user_id', $victim->id)->first();

        $this->actingAs(User::factory()->create())
            ->post(route('questions.playerAnswer', ['game' => $game->id, 'user' => $victim->id]), [
                'question' => ['id' => $victimQuestion->id, 'answer' => 'Hijacked'],
            ])->assertForbidden();
    }
}
