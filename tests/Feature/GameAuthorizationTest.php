<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\Mode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GameAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Game $game;

    private User $host;

    private User $player;

    private Mode $mode;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->mode = Mode::create(['name' => 'Test Mode']);

        $this->game = Game::factory()->create([
            'mode_id' => $this->mode->id,
            'short_url' => Str::random(8),
        ]);

        $this->host = User::factory()->create();
        $this->player = User::factory()->create();

        GameUser::create(['game_id' => $this->game->id, 'user_id' => $this->host->id, 'is_host' => true, 'status' => 'Host']);
        GameUser::create(['game_id' => $this->game->id, 'user_id' => $this->player->id, 'is_host' => false, 'status' => 'Available']);
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: array<string, mixed>}>
     */
    private function managedRequests(): array
    {
        $game = $this->game->id;

        return [
            ['get', route('games.edit', $game), []],
            ['put', route('games.update', $game), ['name' => 'Hijacked', 'mode_id' => $this->mode->id]],
            ['post', route('games.duplicate', $game), []],
            ['delete', route('games.removePlayer', ['game' => $game, 'user' => $this->player->id]), []],
            ['post', route('games.createquestions', $game), []],
            ['get', route('games.startGame', $game), []],
            ['get', route('games.startRound', ['game' => $game, 'round' => 1]), []],
            ['get', route('games.endRound', ['game' => $game, 'round' => 1]), []],
            ['get', route('games.endGame', $game), []],
        ];
    }

    public function test_a_stranger_cannot_view_or_manage_someone_elses_game(): void
    {
        $stranger = User::factory()->create();
        $this->actingAs($stranger);

        $this->get(route('games.show', $this->game))->assertForbidden();

        foreach ($this->managedRequests() as [$method, $url, $data]) {
            $this->{$method}($url, $data)->assertForbidden();
        }

        $this->assertNotSame('Hijacked', $this->game->fresh()->name);
        $this->assertTrue($this->game->players()->where('users.id', $this->player->id)->exists());
    }

    public function test_a_player_can_view_but_not_manage_the_game(): void
    {
        $this->actingAs($this->player);

        $this->get(route('games.show', $this->game))->assertOk();

        foreach ($this->managedRequests() as [$method, $url, $data]) {
            $this->{$method}($url, $data)->assertForbidden();
        }
    }

    public function test_a_player_does_not_see_other_players_emails(): void
    {
        $this->actingAs($this->player);

        $response = $this->get(route('games.show', $this->game));

        $response->assertOk();
        $response->assertDontSee($this->host->email);
        $response->assertInertia(fn (Assert $page) => $page
            ->where('host.email', null)
            ->has('players', 2)
        );
    }

    public function test_the_host_can_view_emails_and_edit_the_game(): void
    {
        $this->actingAs($this->host);

        $this->get(route('games.show', $this->game))
            ->assertOk()
            ->assertSee($this->player->email);

        $this->get(route('games.edit', $this->game))->assertOk();

        $this->put(route('games.update', $this->game), ['name' => 'Renamed', 'mode_id' => $this->mode->id])
            ->assertRedirect(route('games.show', $this->game->id));

        $this->assertSame('Renamed', $this->game->fresh()->name);
    }

    public function test_an_admin_can_manage_any_game(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin);

        $this->get(route('games.show', $this->game))->assertOk();
        $this->get(route('games.edit', $this->game))->assertOk();
    }
}
