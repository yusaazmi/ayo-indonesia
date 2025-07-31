<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'position' => $this->faker->randomElement(['penyerang', 'penjaga gawang', 'gelandang', 'bertahan']),
            'player_number' => $this->faker->numberBetween(1, 99),
            'team_id' => \App\Models\Team::factory(),
            'height_cm' => $this->faker->numberBetween(150, 200),
            'weight_kg' => $this->faker->numberBetween(30, 150),
            'player_number' => $this->faker->unique()->numberBetween(1, 99),
        ];
    }
}
