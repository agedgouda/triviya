<?php

namespace Database\Seeders;

use App\Models\GameProduct;
use Illuminate\Database\Seeder;

class GameProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Starter Pack', 'game_count' => 1, 'price' => 4.99],
            ['name' => 'Triple Play', 'game_count' => 3, 'price' => 9.99],
            ['name' => 'Perfect Ten', 'game_count' => 10, 'price' => 19.99],
        ];

        foreach ($products as $product) {
            GameProduct::create($product);
        }
    }
}
