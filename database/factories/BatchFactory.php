<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Batch;
use App\Models\Station;
use App\Enums\BatchStatus;
use Illuminate\Support\Str;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition()
    {
        return [
            'batch_code' => 'B' . now()->format('Ymd') . strtoupper(Str::random(4)),
            'station_id' => Station::inRandomOrder()->first()->id,
            'created_by' => 1, // Assuming user with ID 1 is the admin or system user
            'arrival_date' => $this->faker->dateTimeBetween('-4 weeks', '-2 weeks'),
            'initial_quantity' => $this->faker->numberBetween(200, 500),
            'current_quantity' => function (array $attributes) {
                return $attributes['initial_quantity'];
            },
            'initial_weight_grams' => $this->faker->numberBetween(40, 50),
            'feeding_method' => $this->faker->randomElement(['twice_daily', 'four_times_daily', 'unlimited']),
            'status' => $this->faker->randomElement(['active', 'sold_out', 'archived']),
        ];
    }
}