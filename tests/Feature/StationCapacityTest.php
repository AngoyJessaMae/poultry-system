<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\MortalityRecord;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationCapacityTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_larger_than_station_capacity_is_rejected(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $station = Station::create([
            'name' => 'Station 1',
            'capacity' => 1000,
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);

        $this->actingAs($worker)
            ->from(route('worker.batches.create'))
            ->post(route('worker.batches.store'), [
                'batch_code' => 'OVERCAPACITY-1500',
                'station_id' => $station->id,
                'arrival_date' => now()->toDateString(),
                'initial_quantity' => 1500,
                'initial_weight_grams' => 45,
                'feeding_method' => 'twice_daily',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('station_id')
            ->assertRedirect(route('worker.batches.create', absolute: false));

        $this->assertDatabaseMissing('batches', ['batch_code' => 'OVERCAPACITY-1500']);
    }

    public function test_mortality_releases_station_capacity_for_another_batch(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $station = Station::create([
            'name' => 'Station 1',
            'capacity' => 1000,
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);
        $batch = Batch::create([
            'batch_code' => 'CAPACITY-1000',
            'station_id' => $station->id,
            'created_by' => $worker->id,
            'arrival_date' => now()->toDateString(),
            'initial_quantity' => 1000,
            'current_quantity' => 1000,
            'initial_weight_grams' => 45,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);

        $this->assertSame(0, $station->fresh()->available_capacity);

        MortalityRecord::create([
            'batch_id' => $batch->id,
            'station_id' => $station->id,
            'user_id' => $worker->id,
            'mortality_date' => now()->toDateString(),
            'count' => 100,
        ]);

        $this->assertSame(100, $station->fresh()->available_capacity);

        $this->actingAs($worker)
            ->post(route('worker.batches.store'), [
                'batch_code' => 'CAPACITY-100',
                'station_id' => $station->id,
                'arrival_date' => now()->toDateString(),
                'initial_quantity' => 100,
                'initial_weight_grams' => 45,
                'feeding_method' => 'twice_daily',
                'status' => 'active',
            ])
            ->assertRedirect(route('worker.batches.index', absolute: false));

        $this->assertDatabaseHas('batches', ['batch_code' => 'CAPACITY-100']);
    }
}