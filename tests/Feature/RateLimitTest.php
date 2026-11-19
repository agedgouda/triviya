<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Mode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    private function createGame(): Game
    {
        return Game::factory()->create([
            'mode_id' => Mode::create(['name' => 'Test Mode'])->id,
            'short_url' => Str::random(8),
        ]);
    }

    public function test_player_login_is_throttled_after_five_attempts(): void
    {
        $game = $this->createGame();
        $user = User::factory()->create();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login.submit', $game), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertStatus(302);
        }

        $this->post(route('login.submit', $game), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_registration_is_throttled_after_six_attempts(): void
    {
        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $this->post(route('register.submit'), ['email' => 'not-an-email'])->assertStatus(302);
        }

        $this->post(route('register.submit'), ['email' => 'not-an-email'])->assertStatus(429);
    }

    public function test_joining_a_game_is_throttled_after_ten_attempts(): void
    {
        $game = $this->createGame();

        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->post(route('questions.join', $game), ['name' => ''])->assertStatus(302);
        }

        $this->post(route('questions.join', $game), ['name' => ''])->assertStatus(429);
    }

    public function test_api_user_route_is_throttled(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        for ($attempt = 1; $attempt <= 60; $attempt++) {
            $this->getJson('/api/user')->assertOk();
        }

        $this->getJson('/api/user')->assertStatus(429);
    }
}
