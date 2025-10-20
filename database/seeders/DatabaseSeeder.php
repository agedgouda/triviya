<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // User::factory(100)->withPersonalTeam()->create();

        // $this->call([
        //     ModeSeeder::class,
        //     QuestionSeeder::class,
        //     ModeQuestionSeeder::class,
        //     GameSeeder::class,

        // ]);
        $this->call(SettingsTableSeeder::class);

    }
}
