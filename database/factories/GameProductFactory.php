<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameProduct>
 */
class GameProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'game_count' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 1, 30),
            'is_active' => true,
        ];
    }
}
