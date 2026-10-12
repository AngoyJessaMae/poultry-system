<?php

namespace Tests\Unit;

use App\Models\Batch;
use App\Models\DailyProductionRecord;
use App\Models\ProductionStandard;
use App\Models\Station;
use App\Models\User;
use App\Services\DailyProductionReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyProductionReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_age_one_rows_and_calculates_cumulative_values(): void
    {
        $user = User::factory()->create(['role' => 'worker', 'is_active' => true]);
        $station = Station::create([
            'name' => 'Production Test Station',
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);
        $loadingDate = Carbon::parse('2026-10-01');
        $batch = Batch::create([
            'batch_code' => 'PROD-TEST',
            'station_id' => $station->id,
            'created_by' => $user->id,
            'flockman_id' => $user->id,
            'arrival_date' => $loadingDate,
            'initial_quantity' => 100,
            'initial_weight_grams' => 40,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);

        ProductionStandard::create([
            'age_days' => 1,
            'mortality_percent' => 1.0,
            'live_weight_grams' => 45,
            'feed_grams' => 1000,
            'fcr' => 0.5,
        ]);
        ProductionStandard::create([
            'age_days' => 2,
            'mortality_percent' => 2.0,
            'live_weight_grams' => 55,
            'feed_grams' => 1200,
            'fcr' => 0.6,
        ]);

        DailyProductionRecord::create([
            'batch_id' => $batch->id,
            'recorded_by' => $user->id,
            'recorded_date' => $loadingDate,
            'mortality_heads' => 2,
            'actual_live_weight_grams' => 50,
            'actual_feed_grams' => 1000,
        ]);
        DailyProductionRecord::create([
            'batch_id' => $batch->id,
            'recorded_by' => $user->id,
            'recorded_date' => $loadingDate->copy()->addDay(),
            'mortality_heads' => 3,
            'actual_live_weight_grams' => 60,
            'actual_feed_grams' => 2000,
        ]);

        $rows = app(DailyProductionReportService::class)->rows($batch, $loadingDate->copy()->addDays(2));

        $this->assertSame([1, 2, 3], array_column($rows, 'age'));
        $this->assertSame(2, $rows[0]['cumulative_deaths']);
        $this->assertSame(5, $rows[1]['cumulative_deaths']);
        $this->assertSame(5.0, $rows[1]['actual_mortality_percent']);
        $this->assertSame(3000.0, $rows[1]['cumulative_actual_feed_grams']);
        $this->assertEqualsWithDelta(3000 / (95 * 60), $rows[1]['actual_fcr'], 0.000001);
        $this->assertNull($rows[2]['actual_live_weight_grams']);
        $this->assertSame(3400.0, $rows[2]['cumulative_standard_feed_grams']);
    }
}