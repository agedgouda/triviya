<?php

namespace Tests\Feature;

use App\Actions\Games\CreateEventQuestionsAction;
use App\Models\Game;
use App\Models\GameProduct;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Models\Mode;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BonusRoundPurchaseTest extends TestCase
{
    use RefreshDatabase;

    private function createGameWithPlayers(User $host, int $playerCount, ?\DateTimeInterface $createdAt = null): Game
    {
        $mode = Mode::create(['name' => 'Test Mode']);

        $game = Game::factory()->create([
            'mode_id' => $mode->id,
            'short_url' => Str::random(8),
            'created_at' => $createdAt ?? now(),
        ]);

        GameUser::create([
            'game_id' => $game->id,
            'user_id' => $host->id,
            'status' => 'Completed',
            'is_host' => true,
        ]);
        $this->addQuestionsForPlayer($game, $host);

        for ($i = 1; $i < $playerCount; $i++) {
            $user = User::factory()->create();

            GameUser::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
                'status' => 'Completed',
                'is_host' => false,
            ]);
            $this->addQuestionsForPlayer($game, $user);
        }

        $game->update(['status' => 'in progress']);

        return $game;
    }

    private function addQuestionsForPlayer(Game $game, User $user): void
    {
        $assignments = [];
        for ($q = 0; $q < 10; $q++) {
            $assignments[] = [
                'game_id' => $game->id,
                'user_id' => $user->id,
                'player_name' => $user->name,
                'question_text' => "Question {$q} for {$user->id}",
                'answer' => 'Some answer',
                'question_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        GameUserQuestions::insert($assignments);
    }

    public function test_first_game_bonus_round_is_locked_when_no_credits_available(): void
    {
        $host = User::factory()->create(['games_available' => 0]);
        $game = $this->createGameWithPlayers($host, 4);
        (new CreateEventQuestionsAction)->handle($game);

        $response = $this->actingAs($host)->get(route('games.endGame', ['game' => $game->id]));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Event/Show')
            ->where('hasBonusQuestionsAvailable', true)
            ->where('bonusLockedForPurchase', true)
        );

        $this->actingAs($host)->get(route('games.startGame', ['game' => $game->id, 'reset' => -1]));

        $this->assertSame(
            0,
            GameUserQuestions::where('game_id', $game->id)->where('question_number', -1)->count()
        );
        $this->assertSame(0, $host->fresh()->games_available);
    }

    public function test_first_game_bonus_round_unlocks_and_spends_one_credit(): void
    {
        $host = User::factory()->create(['games_available' => 1]);
        $game = $this->createGameWithPlayers($host, 4);
        (new CreateEventQuestionsAction)->handle($game);
        $this->actingAs($host)->get(route('games.endGame', ['game' => $game->id]));

        $response = $this->actingAs($host)->get(route('games.startGame', ['game' => $game->id, 'reset' => -1]));

        $response->assertInertia(fn (Assert $page) => $page->component('Event/Show'));
        $this->assertSame(0, $host->fresh()->games_available);
    }

    public function test_second_games_bonus_round_is_never_locked(): void
    {
        $host = User::factory()->create(['games_available' => 0]);

        $firstGame = $this->createGameWithPlayers($host, 4, now()->subMinute());
        (new CreateEventQuestionsAction)->handle($firstGame);

        $secondGame = $this->createGameWithPlayers($host, 4);
        (new CreateEventQuestionsAction)->handle($secondGame);

        $response = $this->actingAs($host)->get(route('games.endGame', ['game' => $secondGame->id]));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Event/Show')
            ->where('hasBonusQuestionsAvailable', true)
            ->where('bonusLockedForPurchase', false)
        );

        $this->actingAs($host)->get(route('games.startGame', ['game' => $secondGame->id, 'reset' => -1]));

        $this->assertGreaterThan(
            0,
            GameUserQuestions::where('game_id', $secondGame->id)->where('question_number', -1)->count()
        );
        $this->assertSame(0, $host->fresh()->games_available);
    }

    public function test_game_creation_never_requires_credits(): void
    {
        $host = User::factory()->create(['games_available' => 0]);
        $mode = Mode::create(['name' => 'Test Mode']);
        Question::factory()->create(['question_type' => 'text'])->modes()->attach($mode->id);

        $response = $this->actingAs($host)->post(route('games.store'), [
            'name' => 'No Credits Needed',
            'mode_id' => $mode->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('games', ['name' => 'No Credits Needed']);
        $this->assertSame(0, $host->fresh()->games_available);
    }

    public function test_purchasing_a_product_increments_games_available(): void
    {
        config(['game.purchasing_enabled' => true]);

        $user = User::factory()->create(['games_available' => 0]);
        $product = GameProduct::create([
            'name' => 'Test Pack',
            'game_count' => 3,
            'price' => 9.99,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('games.purchase.store'), [
            'game_product_id' => $product->id,
        ]);

        $response->assertRedirect(route('games'));
        $this->assertSame(3, $user->fresh()->games_available);
    }

    public function test_purchasing_is_unavailable_when_disabled(): void
    {
        config(['game.purchasing_enabled' => false]);

        $user = User::factory()->create(['games_available' => 0]);
        $product = GameProduct::create([
            'name' => 'Test Pack',
            'game_count' => 3,
            'price' => 9.99,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get(route('games.purchase'))->assertNotFound();
        $this->actingAs($user)->post(route('games.purchase.store'), [
            'game_product_id' => $product->id,
        ])->assertNotFound();

        $this->assertSame(0, $user->fresh()->games_available);
    }
}
