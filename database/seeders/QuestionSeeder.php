<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            ['question_text' => 'When were you born (month, date, year)', 'question_type' => 'date'],
            ['question_text' => 'In what city were you born?', 'question_type' => 'text'],
            ['question_text' => 'If you could only have one food for the rest of your life, what would it be?', 'question_type' => 'text'],
            ['question_text' => 'What was the name of your second grade teacher?', 'question_type' => 'text'],
            ['question_text' => 'What is/was/will be your high school mascot?', 'question_type' => 'text'],
            ['question_text' => 'What was your first concert/live performance?', 'question_type' => 'text'],
            ['question_text' => 'Who is your favorite singer?', 'question_type' => 'text'],
            ['question_text' => 'What is your favorite tv show?', 'question_type' => 'text'],
            ['question_text' => 'What is the first movie you remember seeing in a movie theater?', 'question_type' => 'text'],
            ['question_text' => 'What are you most afraid of?', 'question_type' => 'text'],
            ['question_text' => 'What is your dream job?', 'question_type' => 'text'],
            ['question_text' => 'Have you ever broken a bone, and if so, which one?', 'question_type' => 'text'],
            ['question_text' => 'What is your favorite song?', 'question_type' => 'text'],
            ['question_text' => 'If you could go anywhere in the world, where would it be?', 'question_type' => 'text'],
            ['question_text' => 'Who was your first crush?', 'question_type' => 'text'],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
