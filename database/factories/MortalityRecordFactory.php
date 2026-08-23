<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MortalityRecord;
use App\Models\Batch;
use App\Models\User;

class MortalityRecordFactory extends Factory
{
    protected $model = MortalityRecord::class;

    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'count' => $this->faker->numberBetween(1, 5),
            'cause' => 'Natural Causes',
            'notes' => $this->faker->sentence,
        ];
    }
}