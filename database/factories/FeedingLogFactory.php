<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FeedingLog;
use App\Models\Batch;
use App\Models\User;
use App\Enums\FeedType;

class FeedingLogFactory extends Factory
{
    protected $model = FeedingLog::class;

    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'feed_type' => $this->faker->randomElement(FeedType::cases()),
            'quantity_kg' => $this->faker->randomFloat(2, 1, 10),
            'notes' => $this->faker->sentence,
        ];
    }
}