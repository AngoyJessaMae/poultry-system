<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Sale;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4SalesManagementTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function batch(string $code, string $stationCode, int $quantity = 100): Batch
    {
        $station = Station::create([
            'station_code' => $stationCode,
            'name' => $stationCode . ' Station',
            'capacity' => 1000,
            'min_age_days' => 1,
            'max_age_days' => 100,
        ]);

        return Batch::create([
            'batch_code' => $code,
            'station_id' => $station->id,
            'created_by' => $this->user('manager')->id,
            'arrival_date' => now()->toDateString(),
            'initial_quantity' => $quantity,
            'current_quantity' => $quantity,
            'initial_weight_grams' => 45,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);
    }

    private function payload(int $batchId, int $quantity = 10): array
    {
        return [
            'batch_id' => $batchId,
            'sale_date' => '2026-11-14',
            'heads_sold' => $quantity,
            'total_weight_kg' => 20,
            'price_per_kg' => 150,
            'buyer_name' => 'Buyer',
        ];
    }

    public function test_worker_cannot_access_sales_management(): void
    {
        $worker = $this->user('worker');
        $this->actingAs($worker)->get(route('manager.sales.index'))->assertForbidden();
        $this->actingAs($worker)->post(route('manager.sales.store'), $this->payload(1))->assertForbidden();
    }

    public function test_manager_create_page_contains_only_s3_batches(): void
    {
        $manager = $this->user('manager');
        $s1 = $this->batch('B-S1', 'S1');
        $s2 = $this->batch('B-S2', 'S2');
        $s3 = $this->batch('B-S3', 'S3');

        $this->actingAs($manager)->get(route('manager.sales.create'))
            ->assertOk()
            ->assertSee('B-S3')
            ->assertDontSee('>B-S1 —')
            ->assertDontSee('>B-S2 —');
    }

    public function test_backend_rejects_s1_and_s2_and_s3_sale_deducts_once(): void
    {
        $manager = $this->user('manager');
        $s1 = $this->batch('B-S1', 'S1');
        $s2 = $this->batch('B-S2', 'S2');
        $s3 = $this->batch('B-S3', 'S3', 50);

        $this->actingAs($manager)->post(route('manager.sales.store'), $this->payload($s1->id))->assertSessionHasErrors('batch_id');
        $this->actingAs($manager)->post(route('manager.sales.store'), $this->payload($s2->id))->assertSessionHasErrors('batch_id');
        $this->actingAs($manager)->post(route('manager.sales.store'), $this->payload($s3->id, 12))->assertRedirect();

        $sale = Sale::first();
        $this->assertSame(38, $s3->fresh()->current_quantity);
        $this->assertEquals(1800, (float) $sale->total_amount);
        $this->assertSame('S3', $sale->station->station_code);
    }

    public function test_sale_cannot_exceed_available_quantity(): void
    {
        $manager = $this->user('manager');
        $batch = $this->batch('B-LOW', 'S3', 5);

        $this->actingAs($manager)->post(route('manager.sales.store'), $this->payload($batch->id, 6))
            ->assertSessionHasErrors('batch_id');
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(5, $batch->fresh()->current_quantity);
    }

    public function test_manager_can_edit_completed_sale_and_quantity_delta_is_applied_once(): void
    {
        $manager = $this->user('manager');
        $batch = $this->batch('B-EDIT', 'S3', 50);
        $this->actingAs($manager)->post(route('manager.sales.store'), $this->payload($batch->id, 10))->assertRedirect();
        $sale = Sale::firstOrFail();

        $this->actingAs($manager)->put(route('manager.sales.update', $sale), $this->payload($batch->id, 15))->assertRedirect();
        $this->assertSame(35, $batch->fresh()->current_quantity);
        $this->assertSame(15, $sale->fresh()->heads_sold);
        $this->assertEquals(2250, (float) $sale->fresh()->total_amount);

        $this->actingAs($manager)->put(route('manager.sales.update', $sale), $this->payload($batch->id, 5))->assertRedirect();
        $this->assertSame(45, $batch->fresh()->current_quantity);
    }

    public function test_cancellation_restores_quantity_once_and_excludes_revenue(): void
    {
        $manager = $this->user('manager');
        $batch = $this->batch('B-CANCEL', 'S3', 30);
        $this->actingAs($manager)->post(route('manager.sales.store'), $this->payload($batch->id, 10))->assertRedirect();
        $sale = Sale::firstOrFail();
        $this->assertSame(20, $batch->fresh()->current_quantity);

        $this->actingAs($manager)->post(route('manager.sales.cancel', $sale), ['cancellation_reason' => 'Buyer cancelled'])->assertRedirect();
        $this->assertSame(30, $batch->fresh()->current_quantity);
        $this->assertSame('cancelled', $sale->fresh()->status);
        $this->assertEquals(0, (float) Sale::valid()->sum('total_amount'));

        $this->actingAs($manager)->post(route('manager.sales.cancel', $sale->fresh()), ['cancellation_reason' => 'Duplicate request'])->assertSessionHasErrors('cancellation_reason');
        $this->assertSame(30, $batch->fresh()->current_quantity);
    }
}
