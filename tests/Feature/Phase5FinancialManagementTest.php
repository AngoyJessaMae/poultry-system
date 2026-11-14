<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\FeedPrice;
use App\Models\FeedingLog;
use App\Models\FinancialAdjustment;
use App\Models\OperationalExpense;
use App\Models\OtherIncome;
use App\Models\Sale;
use App\Models\Station;
use App\Models\User;
use App\Services\FinancialSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase5FinancialManagementTest extends TestCase
{
    use RefreshDatabase;

    private function station(string $code): Station
    {
        return Station::create(['station_code' => $code, 'name' => $code . ' Station', 'capacity' => 1000, 'min_age_days' => 1, 'max_age_days' => 100]);
    }

    private function batch(Station $station, string $code = 'B-001'): Batch
    {
        $manager = User::factory()->create(['role' => 'manager']);
        return Batch::create(['batch_code' => $code, 'station_id' => $station->id, 'created_by' => $manager->id, 'arrival_date' => '2026-11-01', 'initial_quantity' => 100, 'current_quantity' => 100, 'initial_weight_grams' => 45, 'feeding_method' => 'unlimited', 'status' => 'active']);
    }

    private function log(Batch $batch, Station $station, User $worker, string $date, float $kg, string $feed = 'Starter'): FeedingLog
    {
        return FeedingLog::create(['batch_id' => $batch->id, 'station_id' => $station->id, 'user_id' => $worker->id, 'feed_type' => $feed, 'quantity' => $kg * 1000, 'quantity_kg' => $kg, 'unit' => 'kg', 'method' => 'Free Choice', 'feeding_time' => $date]);
    }

    public function test_s1_consumption_is_aggregated_and_priced_from_worker_logs(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $manager = User::factory()->create(['role' => 'manager']);
        $station = $this->station('S1');
        $batch = $this->batch($station);
        $this->log($batch, $station, $worker, '2026-11-05 07:00:00', 25);
        $this->log($batch, $station, $worker, '2026-11-06 07:00:00', 15);

        $this->actingAs($manager)->post(route('manager.financial.feed-prices.store'), ['feed_type' => 'Starter', 'price_per_sack' => 2500, 'sack_weight_kg' => 50, 'effective_from' => '2026-11-01'])->assertRedirect();
        $this->actingAs($manager)->get(route('manager.financial.index', ['month' => 11, 'year' => 2026, 'station_id' => $station->id]))->assertOk()->assertSee('Financial Management')->assertSee('40.000');
        $summary = app(FinancialSummaryService::class)->summarize(2026, 11, $station->id, $batch->id, 'Starter');

        $this->assertEquals(40, $summary['feed_kg']);
        $this->assertEquals(0.8, $summary['feed_sacks']);
        $this->assertEquals(2000, $summary['feed_expense']);
        $this->assertEquals(40, $summary['batches']->first()['quantity_kg']);
        $this->assertCount(2, $summary['daily']);
    }

    public function test_station_snapshot_is_used_after_batch_transfer(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $s1 = $this->station('S1');
        $s2 = $this->station('S2');
        $batch = $this->batch($s1);
        $this->log($batch, $s1, $worker, '2026-11-10 07:00:00', 20);
        $batch->update(['station_id' => $s2->id]);
        $this->log($batch, $s2, $worker, '2026-11-20 07:00:00', 30);

        $summary = app(FinancialSummaryService::class)->summarize(2026, 11);
        $this->assertSame(2, $summary['stations']->count());
        $this->assertEquals(20, (float) $summary['stations']->firstWhere('label', 'S1')['quantity_kg']);
        $this->assertEquals(30, (float) $summary['stations']->firstWhere('label', 'S2')['quantity_kg']);
    }

    public function test_effective_price_history_and_financial_totals_are_preserved(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $worker = User::factory()->create(['role' => 'worker']);
        $station = $this->station('S3');
        $batch = $this->batch($station);
        $this->log($batch, $station, $worker, '2026-11-10 07:00:00', 50);
        FeedPrice::create(['feed_type' => 'Starter', 'price_per_sack' => 2500, 'sack_weight_kg' => 50, 'effective_from' => '2026-11-01', 'created_by' => $manager->id]);
        FeedPrice::create(['feed_type' => 'Starter', 'price_per_sack' => 3000, 'sack_weight_kg' => 50, 'effective_from' => '2026-12-01', 'created_by' => $manager->id]);
        Sale::create(['transaction_number' => 'SALE-FIN-1', 'batch_id' => $batch->id, 'station_id' => $station->id, 'user_id' => $manager->id, 'sale_date' => '2026-11-15', 'heads_sold' => 10, 'total_weight_kg' => 20, 'price_per_kg' => 150, 'total_amount' => 1500, 'status' => 'completed']);
        OperationalExpense::create(['description' => 'Vaccines', 'category' => 'Vaccines', 'amount' => 500, 'expense_date' => '2026-11-15', 'allocation_type' => 'overall', 'created_by' => $manager->id, 'status' => 'active']);
        OtherIncome::create(['description' => 'Egg trays', 'category' => 'Other', 'amount' => 200, 'income_date' => '2026-11-18', 'created_by' => $manager->id, 'status' => 'active']);
        FinancialAdjustment::create(['adjustment_type' => 'increase', 'amount' => 100, 'reason' => 'Reconciliation', 'adjustment_date' => '2026-11-20', 'created_by' => $manager->id, 'status' => 'active']);

        $summary = app(FinancialSummaryService::class)->summarize(2026, 11);
        $this->assertEquals(2500, $summary['feed_expense']);
        $this->assertEquals(1500, $summary['sales_revenue']);
        $this->assertEquals(200, $summary['other_income']);
        $this->assertEquals(500, $summary['other_expenses']);
        $this->assertEquals(100, $summary['net_adjustments']);
        $this->assertEquals(-1200, $summary['net_profit']);
    }

    public function test_workers_cannot_access_financial_management(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $this->actingAs($worker)->get(route('manager.financial.index'))->assertForbidden();
        $this->actingAs($worker)->post(route('manager.financial.feed-prices.store'), [])->assertForbidden();
        $this->actingAs($worker)->post(route('manager.financial.expenses.store'), [])->assertForbidden();
    }
}
