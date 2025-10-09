<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run()
    {
        // Ensure a user with id = 1 exists (optional safety check)
        $host = User::find('9d957658-b52b-49a8-9b15-d28dcf3aecb7');

        if (! $host) {
            $host = User::factory()->create(['id' => '9d957658-b52b-49a8-9b15-d28dcf3aecb7']); // Create the user if it doesn't exist
        }

        // Create 5 games and associate them with the host
        $games = Game::factory(5)->create();

        foreach ($games as $game) {
            $game->players()->attach($host->id, ['is_host' => true]);
        }
    }
}
