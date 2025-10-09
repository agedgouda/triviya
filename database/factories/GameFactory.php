<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = \App\Models\Game::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'date_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'mode_id' => $this->faker->numberBetween(1, 3),
            'location' => $this->faker->city.', '.$this->faker->state.', '.$this->faker->country,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
