<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'Admin'],
            ['name' => 'Position 1'],
            ['name' => 'Position 2'],
            ['name' => 'Position 3'],
            ['name' => 'Position 4'],
            ['name' => 'Position 5'],
            ['name' => 'Position 6'],
            ['name' => 'Position 7'],
            ['name' => 'Position 8'],
            ['name' => 'Position 9'],
            ['name' => 'Position 10'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}