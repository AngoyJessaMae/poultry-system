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
            'affected_count' => 1,
            'dead_count' => 0,
            'status' => 'under_treatment',
            'recorded_date' => now(),
            'observation' => 'Routine Check',
            'medication_name' => null,
            'dosage_amount' => null,
            'dosage_unit' => null,
            'notes' => $this->faker->sentence,
            'remarks' => null,
            'remedy' => null,
            'mortality_record_id' => null,
        ];
    }
}