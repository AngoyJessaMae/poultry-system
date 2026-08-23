<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Station;

class StationFactory extends Factory
{
    protected $model = Station::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word . ' Station',
            'min_age_days' => $this->faker->numberBetween(1, 10),
            'max_age_days' => $this->faker->numberBetween(11, 20),
            'description' => $this->faker->sentence,
        ];
    }
}