<?php

namespace Database\Seeders;

use App\Models\Mode;
use App\Models\Question;
use Illuminate\Database\Seeder;

class ModeQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modes = Mode::all();
        $questions = Question::all();

        // Assign each question to 1-3 random modes
        foreach ($questions as $question) {
            $question->modes()->attach(
                $modes->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
