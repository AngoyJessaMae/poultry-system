<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GrowthRecord;
use App\Models\Batch;
use App\Models\User;

class GrowthRecordFactory extends Factory
{
    protected $model = GrowthRecord::class;

    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'average_weight_grams' => $this->faker->numberBetween(100, 2500),
            'notes' => $this->faker->sentence,
        ];
    }
}