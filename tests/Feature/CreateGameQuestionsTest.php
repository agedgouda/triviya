<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\Mode;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateGameQuestionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function test_it_builds_event_questions_from_player_answers(): void
    {
        $mode = Mode::create(['name' => 'Test Mode']);

        $game = Game::factory()->create([
            'mode_id' => $mode->id,
            'short_url' => Str::random(8),
        ]);

        $host = User::factory()->create();
        $hostGameUser = GameUser::create([
            'game_id' => $game->id,
            'user_id' => $host->id,
            'status' => 'Completed',
            'is_host' => true,
        ]);

        $question = Question::factory()->create(['question_type' => 'text']);

        Answer::create([
            'game_user_id' => $hostGameUser->id,
            'question_id' => $question->id,
            'answer' => 'My answer',
        ]);

        $response = $this->actingAs($host)->post(route('games.createquestions', $game));

        $response->assertOk();
        $response->assertJson(['status' => 'success']);

        $this->assertSame(
            1,
            DB::table('game_user_question')->where('game_id', $game->id)->count()
        );
    }
}
