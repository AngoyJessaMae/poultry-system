<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\BatchMovement;
use App\Models\MortalityRecord;
use App\Models\QuantityAdjustment;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1QuantityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_cannot_change_current_quantity_through_batch_update(): void
    {
        [$worker, $batch] = $this->batchFor('worker', 100);

        $this->actingAs($worker)
            ->patch(route('worker.batches.update', $batch), [
                'current_quantity' => 999,
                'batch_code' => $batch->batch_code,
            ])
            ->assertForbidden();

        $this->assertSame(100, $batch->fresh()->current_quantity);
    }

    public function test_manager_correction_requires_a_history_record_and_previous_quantity(): void
    {
        [$manager, $batch] = $this->batchFor('manager', 100);

        $this->actingAs($manager)
            ->post(route('manager.quantity-adjustments.store', $batch), [
                'previous_quantity' => 100,
                'corrected_quantity' => 92,
                'reason' => 'Physical count reconciliation',
            ])
            ->assertRedirect();

        $this->assertSame(92, $batch->fresh()->current_quantity);
        $this->assertDatabaseHas('quantity_adjustments', [
            'batch_id' => $batch->id,
            'manager_id' => $manager->id,
            'previous_quantity' => 100,
            'corrected_quantity' => 92,
            'reason' => 'Physical count reconciliation',
        ]);
    }

    public function test_worker_cannot_record_manager_quantity_correction(): void
    {
        [$worker, $batch] = $this->batchFor('worker', 100);

        $this->actingAs($worker)
            ->post(route('manager.quantity-adjustments.store', $batch), [
                'previous_quantity' => 100,
                'corrected_quantity' => 92,
                'reason' => 'Unauthorised correction',
            ])
            ->assertForbidden();
    }

    public function test_mortality_decreases_quantity_once(): void
    {
        [$worker, $batch] = $this->batchFor('worker', 100);

        $this->actingAs($worker)
            ->post(route('worker.mortality-records.store'), [
                'batch_id' => $batch->id,
                'mortality_date' => now()->toDateString(),
                'count' => 7,
            ])
            ->assertRedirect();

        $this->assertSame(93, $batch->fresh()->current_quantity);
        $this->assertDatabaseCount('mortality_records', 1);
    }

    public function test_completed_sale_decreases_quantity_once(): void
    {
        [$manager, $batch] = $this->batchFor('manager', 100);
        $batch->station->update(['station_code' => 'S3']);

        $this->actingAs($manager)
            ->post(route('manager.sales.store'), [
                'batch_id' => $batch->id,
                'sale_date' => now()->toDateString(),
                'heads_sold' => 12,
                'total_weight_kg' => 24,
                'price_per_kg' => 150,
                'total_amount' => 3600,
                'buyer_name' => 'Test buyer',
            ])
            ->assertRedirect();

        $this->assertSame(88, $batch->fresh()->current_quantity);
        $this->assertDatabaseCount('sales', 1);
    }

    public function test_whole_batch_station_transfer_preserves_quantity_and_records_transfer_quantity(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $source = Station::create(['name' => 'Source', 'capacity' => 200, 'min_age_days' => 1, 'max_age_days' => 60]);
        $destination = Station::create(['name' => 'Destination', 'capacity' => 200, 'min_age_days' => 1, 'max_age_days' => 60]);
        $batch = Batch::create([
            'batch_code' => 'TRANSFER-' . uniqid(), 'station_id' => $source->id, 'created_by' => $worker->id,
            'arrival_date' => now()->toDateString(), 'initial_quantity' => 40, 'current_quantity' => 40,
            'initial_weight_grams' => 45, 'feeding_method' => 'twice_daily', 'status' => 'active',
        ]);

        $this->actingAs($worker)
            ->patch(route('worker.batches.update', $batch), ['station_id' => $destination->id])
            ->assertRedirect();

        $this->assertSame($destination->id, $batch->fresh()->station_id);
        $this->assertSame(40, $batch->fresh()->current_quantity);
        $this->assertDatabaseHas('batch_movements', [
            'batch_id' => $batch->id, 'from_station_id' => $source->id,
            'to_station_id' => $destination->id, 'quantity' => 40,
        ]);
        $this->assertDatabaseCount('batch_movements', 1);
    }

    private function batchFor(string $role, int $quantity): array
    {
        $user = User::factory()->create(['role' => $role]);
        $station = Station::create(['name' => ucfirst($role) . ' Station', 'capacity' => 1000, 'min_age_days' => 1, 'max_age_days' => 60]);
        $batch = Batch::create([
            'batch_code' => strtoupper($role) . '-' . uniqid(), 'station_id' => $station->id, 'created_by' => $user->id,
            'arrival_date' => now()->toDateString(), 'initial_quantity' => $quantity, 'current_quantity' => $quantity,
            'initial_weight_grams' => 45, 'feeding_method' => 'twice_daily', 'status' => 'active',
        ]);

        return [$user, $batch];
    }
}
