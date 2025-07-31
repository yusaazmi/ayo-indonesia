<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Player::factory()->count(50)->create([
            'team_id' => function () {
                return \App\Models\Team::inRandomOrder()->first()->id;
            }
        ]);
    }
}
