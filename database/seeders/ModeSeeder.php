<?php

namespace Database\Seeders;

use App\Models\Mode;
use Illuminate\Database\Seeder;

class ModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modes = [
            ['name' => 'Family Ties'],
            ['name' => 'Mix It Up'],
            ['name' => 'Let\'s Mingle'],
        ];

        foreach ($modes as $mode) {
            Mode::create($mode);
        }
    }
}
