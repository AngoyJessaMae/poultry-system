<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\GrowthRecord;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_with_associated_records_is_not_deleted(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'is_active' => true,
        ]);
        $station = Station::create([
            'name' => 'Batch Test Station',
            'capacity' => 1500,
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);
        $batch = Batch::create([
            'batch_code' => 'BATCH-DELETE-TEST',
            'station_id' => $station->id,
            'created_by' => $worker->id,
            'arrival_date' => now()->toDateString(),
            'initial_quantity' => 10,
            'current_quantity' => 10,
            'initial_weight_grams' => 45,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);
        $growthRecord = GrowthRecord::create([
            'batch_id' => $batch->id,
            'user_id' => $worker->id,
            'recorded_date' => now()->toDateString(),
            'age_days' => 1,
            'average_weight_grams' => 50,
            'growth_stage' => 'chick',
        ]);

        $this->actingAs($worker)
            ->delete(route('worker.batches.destroy', $batch))
            ->assertRedirect(route('worker.batches.index', absolute: false))
            ->assertSessionHasErrors('batch');

        $this->assertDatabaseHas('batches', ['id' => $batch->id]);
        $this->assertDatabaseHas('growth_records', ['id' => $growthRecord->id, 'batch_id' => $batch->id]);
    }
}