<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Mode;
use App\Models\User;
use App\Services\SafeRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SafeRedirectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string|null, 1: string}>
     */
    public static function redirectProvider(): array
    {
        return [
            'relative path' => ['/games', '/games'],
            'relative path with query' => ['/questions/abc/show?x=1', '/questions/abc/show?x=1'],
            'other host' => ['https://evil.example/phish', '/fallback'],
            'protocol relative' => ['//evil.example/phish', '/fallback'],
            'backslash trick' => ['/\\evil.example', '/fallback'],
            'javascript scheme' => ['javascript:alert(1)', '/fallback'],
            'lookalike host' => ['http://localhost.evil.example/', '/fallback'],
            'empty' => ['', '/fallback'],
            'null' => [null, '/fallback'],
        ];
    }

    #[DataProvider('redirectProvider')]
    public function test_safe_redirect_only_allows_same_site_urls(?string $input, string $expected): void
    {
        $this->assertSame($expected, SafeRedirect::to($input, '/fallback'));
    }

    public function test_safe_redirect_allows_absolute_urls_on_this_host(): void
    {
        $url = url('/games');

        $this->assertSame($url, SafeRedirect::to($url, '/fallback'));
    }

    public function test_player_login_ignores_offsite_redirect(): void
    {
        $game = Game::factory()->create(['mode_id' => Mode::create(['name' => 'Test Mode'])->id]);
        $user = User::factory()->create();

        $response = $this->post(route('login.submit', $game), [
            'email' => $user->email,
            'password' => 'password',
            'redirect_to' => 'https://evil.example/phish',
        ]);

        $response->assertRedirect(route('questions.showQuestions', ['game' => $game->id]));
    }

    public function test_registration_ignores_offsite_redirect(): void
    {
        $response = $this->post(route('register.submit'), [
            'first_name' => 'New',
            'last_name' => 'Player',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'redirect_to' => '//evil.example/phish',
        ]);

        $response->assertRedirect(route('games'));
    }

    public function test_registration_keeps_onsite_redirect(): void
    {
        $response = $this->post(route('register.submit'), [
            'first_name' => 'New',
            'last_name' => 'Player',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'redirect_to' => '/games/create',
        ]);

        $response->assertRedirect('/games/create');
    }
}
