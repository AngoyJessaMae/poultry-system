<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FeedingLog;
use App\Models\Batch;
use App\Models\User;

class FeedingLogFactory extends Factory
{
    protected $model = FeedingLog::class;

    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'feed_type' => $this->faker->randomElement(['Starter', 'Grower', 'Finisher']),
            'quantity' => $this->faker->randomFloat(3, 1000, 10000),
            'quantity_kg' => fn (array $attributes) => round($attributes['quantity'] / 1000, 3),
            'unit' => 'kg',
            'method' => 'Restricted',
            'feeding_time' => now(),
            'notes' => $this->faker->sentence,
        ];
    }
}
