<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameUserQuestions;
use App\Models\Mode;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnswerLockingTest extends TestCase
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
            'short_url' => Str::random(8),
        ]);

        $questions = Question::factory()->count($questionCount)->create();
        $game->questions()->attach($questions->pluck('id'));

        return $game;
    }

    private function joinAsGuest(Game $game, string $name = 'Mike'): User
    {
        $this->post(route('questions.join', $game), ['name' => $name]);

        return User::where('first_name', $name)->firstOrFail();
    }

    public function test_an_answer_can_be_changed_while_the_quiz_is_still_in_progress(): void
    {
        $game = $this->createGameWithQuestions();
        $guest = $this->joinAsGuest($game);

        $question = GameUserQuestions::where('game_id', $game->id)->where('user_id', $guest->id)->first();

        $this->post(route('questions.playerAnswer', ['game' => $game->id, 'user' => $guest->id]), [
            'question' => ['id' => $question->id, 'answer' => 'First answer'],
        ]);

        $response = $this->post(route('questions.playerAnswer', ['game' => $game->id, 'user' => $guest->id]), [
            'question' => ['id' => $question->id, 'answer' => 'Changed my mind'],
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'success']);
        $this->assertSame('Changed my mind', $question->fresh()->answer);
    }

    public function test_an_answer_cannot_be_changed_once_the_quiz_is_completed(): void
    {
        $game = $this->createGameWithQuestions();
        $guest = $this->joinAsGuest($game);

        GameUserQuestions::where('game_id', $game->id)
            ->where('user_id', $guest->id)
            ->update(['answer' => 'An answer']);

        $question = GameUserQuestions::where('game_id', $game->id)->where('user_id', $guest->id)->first();

        $response = $this->post(route('questions.playerAnswer', ['game' => $game->id, 'user' => $guest->id]), [
            'question' => ['id' => $question->id, 'answer' => 'Too late'],
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'error']);
        $this->assertSame('An answer', $question->fresh()->answer);
    }

    public function test_player_who_has_completed_all_questions_is_redirected_to_thank_you(): void
    {
        $game = $this->createGameWithQuestions();
        $guest = $this->joinAsGuest($game);

        GameUserQuestions::where('game_id', $game->id)
            ->where('user_id', $guest->id)
            ->update(['answer' => 'An answer']);

        $response = $this->get(route('questions.showQuestions', ['game' => $game->id, 'player' => $guest->id]));

        $response->assertRedirect(route('questions.showThankYou', ['game' => $game->id, 'user' => $guest->id]));
    }

    public function test_landing_page_sends_completed_player_straight_to_thank_you(): void
    {
        $game = $this->createGameWithQuestions();
        $guest = $this->joinAsGuest($game);

        GameUserQuestions::where('game_id', $game->id)
            ->where('user_id', $guest->id)
            ->update(['answer' => 'An answer']);

        $response = $this->get(route('questions.showQuestionLanding', $game).'?player='.$guest->id);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Questionnaire/Show')
            ->where('routeName', 'questions.showThankYou')
        );
    }

    public function test_player_with_partial_progress_still_reaches_the_questions_page(): void
    {
        $game = $this->createGameWithQuestions();
        $guest = $this->joinAsGuest($game);

        $firstQuestion = GameUserQuestions::where('game_id', $game->id)->where('user_id', $guest->id)->first();
        $firstQuestion->update(['answer' => 'An answer']);

        $response = $this->get(route('questions.showQuestions', ['game' => $game->id, 'player' => $guest->id]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Questionnaire/Show')
            ->where('routeName', 'questions.showQuestions')
        );
    }
}
