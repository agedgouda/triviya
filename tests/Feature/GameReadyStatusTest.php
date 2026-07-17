<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\Mode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class GameReadyStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    private function createGame(): Game
    {
        $mode = Mode::create(['name' => 'Test Mode']);

        return Game::factory()->create([
            'mode_id' => $mode->id,
            'short_url' => Str::random(8),
        ]);
    }

    public function test_game_becomes_ready_once_two_players_have_completed_their_quiz(): void
    {
        $game = $this->createGame();

        foreach (range(1, 2) as $i) {
            GameUser::create([
                'game_id' => $game->id,
                'user_id' => User::factory()->create()->id,
                'status' => 'Completed',
                'is_host' => false,
            ]);
        }

        $this->assertSame('ready', $game->fresh()->status);
    }

    public function test_game_is_not_ready_with_only_one_completed_player(): void
    {
        $game = $this->createGame();

        GameUser::create([
            'game_id' => $game->id,
            'user_id' => User::factory()->create()->id,
            'status' => 'Completed',
            'is_host' => false,
        ]);

        $this->assertSame('new', $game->fresh()->status);
    }

    public function test_game_is_not_ready_if_a_player_has_not_completed_their_quiz(): void
    {
        $game = $this->createGame();

        GameUser::create([
            'game_id' => $game->id,
            'user_id' => User::factory()->create()->id,
            'status' => 'Completed',
            'is_host' => false,
        ]);

        GameUser::create([
            'game_id' => $game->id,
            'user_id' => User::factory()->create()->id,
            'status' => 'Take Quiz',
            'is_host' => false,
        ]);

        $this->assertSame('new', $game->fresh()->status);
    }
}
