<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_dead_chickens_from_a_health_record_are_added_to_mortality_records(): void
    {
        [$worker, $batch] = $this->workerAndBatch(20);

        $this->actingAs($worker)->post(route('worker.health-records.store'), $this->healthData($batch, [
            'affected_count' => 5,
            'dead_count' => 2,
            'status' => 'recovering',
        ]))->assertRedirect(route('worker.health-records.index', absolute: false));

        $healthRecord = $worker->healthRecords()->first();

        $this->assertDatabaseHas('mortality_records', [
            'batch_id' => $batch->id,
            'count' => 2,
        ]);
        $this->assertSame(18, $batch->fresh()->current_quantity);
        $this->assertNotNull($healthRecord->fresh()->mortality_record_id);
        $this->assertDatabaseHas('health_record_histories', [
            'health_record_id' => $healthRecord->id,
            'user_id' => $worker->id,
            'affected_count' => 5,
            'dead_count' => 2,
            'recovered_count' => 3,
            'status' => 'recovering',
        ]);
    }

    public function test_recovering_health_record_can_be_updated_without_dead_chickens(): void
    {
        [$worker, $batch] = $this->workerAndBatch(20);

        $this->actingAs($worker)->post(route('worker.health-records.store'), $this->healthData($batch, [
            'affected_count' => 4,
            'dead_count' => 2,
            'status' => 'recovering',
        ]));

        $healthRecord = $worker->healthRecords()->first();

        $this->actingAs($worker)->patch(route('worker.health-records.update', $healthRecord), $this->healthData($batch, [
            'affected_count' => 4,
            'dead_count' => 0,
            'status' => 'recovered',
        ]))->assertRedirect(route('worker.health-records.index', absolute: false));

        $this->assertDatabaseCount('mortality_records', 0);
        $this->assertSame(20, $batch->fresh()->current_quantity);
        $this->assertNull($healthRecord->fresh()->mortality_record_id);
        $this->assertDatabaseCount('health_record_histories', 2);
        $this->assertDatabaseHas('health_record_histories', [
            'health_record_id' => $healthRecord->id,
            'affected_count' => 4,
            'dead_count' => 0,
            'recovered_count' => 4,
            'status' => 'recovered',
        ]);
    }

    public function test_worker_can_update_a_health_record_older_than_24_hours(): void
    {
        [$worker, $batch] = $this->workerAndBatch(20);

        $this->actingAs($worker)->post(route('worker.health-records.store'), $this->healthData($batch));

        $healthRecord = $worker->healthRecords()->first();
        $healthRecord->forceFill([
            'created_at' => now()->subDays(2),
        ])->save();

        $this->actingAs($worker)->patch(route('worker.health-records.update', $healthRecord), $this->healthData($batch, [
            'observation' => 'Updated after review',
        ]))->assertRedirect(route('worker.health-records.index', absolute: false));

        $this->assertSame('Updated after review', $healthRecord->fresh()->observation);
    }

    private function workerAndBatch(int $quantity): array
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'is_active' => true,
        ]);
        $station = Station::create([
            'name' => 'Health Test Station',
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);
        $batch = Batch::create([
            'batch_code' => 'HEALTH-' . uniqid(),
            'station_id' => $station->id,
            'created_by' => $worker->id,
            'arrival_date' => now()->subDays(10),
            'initial_quantity' => $quantity,
            'initial_weight_grams' => 45,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);

        return [$worker, $batch];
    }

    private function healthData(Batch $batch, array $overrides = []): array
    {
        return array_merge([
            'batch_id' => $batch->id,
            'recorded_date' => now()->format('Y-m-d'),
            'affected_count' => 1,
            'dead_count' => 0,
            'status' => 'under_treatment',
            'observation' => 'Respiratory symptoms',
            'medication_name' => 'Supportive care',
            'dosage_amount' => null,
            'dosage_unit' => null,
            'notes' => 'Observed during morning check.',
            'remarks' => 'Monitor closely.',
            'remedy' => 'Separate and provide treatment.',
        ], $overrides);
    }
}
