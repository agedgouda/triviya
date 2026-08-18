<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Mode;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateTestGameTest extends TestCase
{
    use RefreshDatabase;

    private function seedMode(): Mode
    {
        $mode = Mode::create(['name' => 'Test Mode']);

        // Enough distinct questions for a 12-player game (10 each).
        for ($i = 0; $i < 120; $i++) {
            Question::factory()->create([
                'question_text' => "Generated question {$i}",
                'question_type' => 'text',
            ])->modes()->attach($mode->id);
        }

        return $mode;
    }

    public function test_non_admin_cannot_generate_a_test_game(): void
    {
        $this->seedMode();
        $user = User::factory()->create(['is_admin' => 0]);

        $response = $this->actingAs($user)->post(route('games.generateTest'), ['player_count' => 5]);

        $response->assertNotFound();
        $this->assertSame(0, Game::count());
    }

    public function test_admin_can_generate_a_fully_answered_test_game(): void
    {
        $this->seedMode();
        $admin = User::factory()->create(['is_admin' => 1]);

        $response = $this->actingAs($admin)->post(route('games.generateTest'), ['player_count' => 5]);

        $response->assertRedirect(route('games'));

        $game = Game::firstOrFail();
        $this->assertSame(5, $game->players()->count());
        $this->assertSame('ready', $game->status);

        foreach ($game->players()->withPivot('status')->get() as $player) {
            $this->assertSame('Completed', $player->pivot->status);
        }
    }

    public function test_player_count_must_be_within_allowed_range(): void
    {
        $this->seedMode();
        $admin = User::factory()->create(['is_admin' => 1]);

        $response = $this->actingAs($admin)->post(route('games.generateTest'), ['player_count' => 2]);

        $response->assertSessionHasErrors('player_count');
        $this->assertSame(0, Game::count());
    }
}
