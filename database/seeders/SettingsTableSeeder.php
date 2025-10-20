<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;


class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $inviteText = "I’m inviting you to play a new game called TriviYa - a party game where we are the trivia.\n\nAll you have to do is register and answer 10 fun questions about yourself. TriviYa does the rest.\n\nSee you at the party.";

        Setting::updateOrCreate(
            ['key' => 'invite_message'],
            ['value' => $inviteText]
        );
    }
}
