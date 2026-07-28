<?php

namespace Tests\Feature;

use App\Actions\Games\CreateEventQuestionsAction;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Models\Mode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EventRoundsTest extends TestCase
{
    use RefreshDatabase;

    private function createGameWithPlayers(int $playerCount): Game
    {
        $mode = Mode::create(['name' => 'Test Mode']);

        $game = Game::factory()->create([
            'mode_id' => $mode->id,
            'short_url' => Str::random(8),
        ]);

        for ($i = 0; $i < $playerCount; $i++) {
            $user = User::factory()->create();

            GameUser::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
                'status' => 'Completed',
                'is_host' => $i === 0,
            ]);

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

        return $game;
    }

    public function test_two_players_get_two_rounds_and_no_bonus(): void
    {
        $game = $this->createGameWithPlayers(2);

        (new CreateEventQuestionsAction)->handle($game);

        $this->assertSame(
            20,
            GameUserQuestions::where('game_id', $game->id)->where('question_number', '>', 0)->count()
        );
        $this->assertSame(2, $game->fresh()->totalRounds());
        $this->assertFalse($game->fresh()->hasBonusQuestionsAvailable());
    }

    public function test_three_players_get_three_rounds_and_no_bonus(): void
    {
        $game = $this->createGameWithPlayers(3);

        (new CreateEventQuestionsAction)->handle($game);

        $this->assertSame(
            30,
            GameUserQuestions::where('game_id', $game->id)->where('question_number', '>', 0)->count()
        );
        $this->assertSame(3, $game->fresh()->totalRounds());
        $this->assertFalse($game->fresh()->hasBonusQuestionsAvailable());
    }

    public function test_four_players_get_three_rounds_plus_bonus_questions(): void
    {
        $game = $this->createGameWithPlayers(4);

        (new CreateEventQuestionsAction)->handle($game);

        $this->assertSame(
            30,
            GameUserQuestions::where('game_id', $game->id)->where('question_number', '>', 0)->count()
        );
        $this->assertSame(3, $game->fresh()->totalRounds());
        $this->assertTrue($game->fresh()->hasBonusQuestionsAvailable());
    }

    public function test_end_game_response_reflects_bonus_availability_for_small_games(): void
    {
        $game = $this->createGameWithPlayers(2);
        $host = $game->host()->first();

        (new CreateEventQuestionsAction)->handle($game);
        $game->update(['status' => 'in progress']);

        $response = $this->actingAs($host)->get(route('games.endGame', ['game' => $game->id]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Event/Show')
            ->where('hasBonusQuestionsAvailable', false)
        );
    }
}
