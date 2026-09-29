<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_station_with_assigned_batches_is_not_deleted(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'is_active' => true,
        ]);
        $station = Station::create([
            'name' => 'Occupied Station',
            'capacity' => 1500,
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);
        $batch = Batch::create([
            'batch_code' => 'STATION-TEST',
            'station_id' => $station->id,
            'created_by' => $worker->id,
            'arrival_date' => now()->toDateString(),
            'initial_quantity' => 10,
            'current_quantity' => 10,
            'initial_weight_grams' => 45,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);

        $this->actingAs($worker)
            ->delete(route('worker.stations.destroy', $station))
            ->assertRedirect(route('worker.stations.index', absolute: false))
            ->assertSessionHasErrors('station');

        $this->assertDatabaseHas('stations', ['id' => $station->id]);
        $this->assertDatabaseHas('batches', ['id' => $batch->id, 'station_id' => $station->id]);
    }
}