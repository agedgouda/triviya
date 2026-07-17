<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\GameUserQuestions;
use App\Models\Mode;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GuestJoinGameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    private function createGameWithQuestions(int $questionCount = 12): Game
    {
        $mode = Mode::create(['name' => 'Test Mode']);

        $game = Game::factory()->create([
            'mode_id' => $mode->id,
            'short_url' => \Illuminate\Support\Str::random(8),
        ]);

        $questions = Question::factory()->count($questionCount)->create();
        $game->questions()->attach($questions->pluck('id'));

        return $game;
    }

    public function test_landing_page_shows_join_form_when_game_is_open(): void
    {
        $game = $this->createGameWithQuestions();

        $response = $this->get(route('questions.showQuestionLanding', $game));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Questionnaire/Show')
            ->where('routeName', 'questions.showQuestionLanding')
            ->where('user', null)
        );
    }

    public function test_guest_can_join_game_with_a_name(): void
    {
        $game = $this->createGameWithQuestions();

        $response = $this->post(route('questions.join', $game), ['name' => 'Mike']);

        $guest = User::where('first_name', 'Mike')->where('is_guest', true)->firstOrFail();

        $response->assertRedirect(route('questions.showQuestions', ['game' => $game->id, 'player' => $guest->id]));

        $this->assertNull($guest->email);
        $this->assertTrue(
            GameUser::where('game_id', $game->id)->where('user_id', $guest->id)->exists()
        );
        $this->assertSame(
            10,
            GameUserQuestions::where('game_id', $game->id)->where('user_id', $guest->id)->count()
        );
    }

    public function test_guest_cannot_join_with_a_name_already_taken_in_the_game(): void
    {
        $game = $this->createGameWithQuestions();

        $this->post(route('questions.join', $game), ['name' => 'Mike']);
        $response = $this->post(route('questions.join', $game), ['name' => 'mike']);

        $response->assertSessionHasErrors('name');
        $this->assertSame(1, User::where('first_name', 'Mike')->count());
    }

    public function test_guest_cannot_join_a_full_game(): void
    {
        $game = $this->createGameWithQuestions();

        for ($i = 0; $i < 12; $i++) {
            $user = User::factory()->create();
            GameUser::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
                'status' => 'Take Quiz',
                'is_host' => false,
            ]);
        }

        $response = $this->post(route('questions.join', $game), ['name' => 'Latecomer']);

        $response->assertRedirect(route('questions.showQuestionLanding', $game));
        $this->assertFalse(User::where('first_name', 'Latecomer')->exists());
    }

    public function test_guest_can_answer_questions_and_view_thank_you_without_authentication(): void
    {
        $game = $this->createGameWithQuestions();

        $this->post(route('questions.join', $game), ['name' => 'Mike']);
        $guest = User::where('first_name', 'Mike')->firstOrFail();

        $question = GameUserQuestions::where('game_id', $game->id)->where('user_id', $guest->id)->first();

        $answerResponse = $this->post(route('questions.playerAnswer', ['game' => $game->id, 'user' => $guest->id]), [
            'question' => ['id' => $question->id, 'answer' => 'My answer'],
        ]);

        $answerResponse->assertOk();
        $this->assertSame('My answer', $question->fresh()->answer);

        $thankYouResponse = $this->get(route('questions.showThankYou', ['game' => $game->id, 'user' => $guest->id]));
        $thankYouResponse->assertOk();
    }

    public function test_returning_guest_is_resolved_via_player_query_parameter(): void
    {
        $game = $this->createGameWithQuestions();

        $this->post(route('questions.join', $game), ['name' => 'Mike']);
        $guest = User::where('first_name', 'Mike')->firstOrFail();

        $response = $this->get(route('questions.showQuestionLanding', $game).'?player='.$guest->id);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Questionnaire/Show')
            ->where('routeName', 'questions.showQuestions')
            ->where('user.id', $guest->id)
        );
    }
}
