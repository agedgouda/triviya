<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\User;

class GameSeeder extends Seeder
{
    public function run()
    {
        // Ensure a user with id = 1 exists (optional safety check)
        $host = User::find(1);

        if (!$host) {
            $host = User::factory()->create(['id' => 1]); // Create the user if it doesn't exist
        }

        // Create 5 games and associate them with the host
        $games = Game::factory(5)->create();

        foreach ($games as $game) {
            $game->players()->attach($host->id, ['is_host' => true]);
        }
    }
}
