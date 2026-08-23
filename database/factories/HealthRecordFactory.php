<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HealthRecord;
use App\Models\Batch;
use App\Models\User;

class HealthRecordFactory extends Factory
{
    protected $model = HealthRecord::class;

    public function definition()
    {
        return [
            'batch_id' => Batch::factory(),
            'user_id' => User::factory(),
            'symptoms' => 'Routine Check',
            'treatment' => 'None',
            'notes' => $this->faker->sentence,
        ];
    }
}