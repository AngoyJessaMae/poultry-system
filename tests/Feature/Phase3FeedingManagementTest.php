<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\Station;
use App\Models\StationFeedingConfig;
use App\Models\User;
use App\Policies\FeedingLogPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class Phase3FeedingManagementTest extends TestCase
{
    use RefreshDatabase;

    private function worker(): User
    {
        return User::factory()->create(['role' => 'worker']);
    }

    private function manager(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    private function station(string $code, string $name): Station
    {
        return Station::create([
            'station_code' => $code,
            'name' => $name,
            'capacity' => 10000,
            'min_age_days' => 1,
            'max_age_days' => 100,
        ]);
    }

    private function batch(Station $station, string $code = 'B-001'): Batch
    {
        return Batch::create([
            'batch_code' => $code,
            'station_id' => $station->id,
            'created_by' => $this->manager()->id,
            'arrival_date' => '2026-11-14',
            'initial_quantity' => 100,
            'initial_weight_grams' => 45,
            'feeding_method' => 'unlimited',
            'status' => 'active',
        ]);
    }

    public function test_s1_allows_multiple_records_and_converts_quantities_to_kg(): void
    {
        $worker = $this->worker();
        $station = $this->station('S1', 'Chick Station');
        $batch = $this->batch($station);

        Carbon::setTestNow('2026-11-14 07:00:00');
        $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), [
            'feed_type' => 'Starter', 'quantity' => '10.25', 'unit' => 'kg',
        ])->assertRedirect();

        Carbon::setTestNow('2026-11-14 10:30:00');
        $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), [
            'feed_type' => 'Starter', 'quantity' => '500', 'unit' => 'g',
        ])->assertRedirect();

        $this->assertSame(2, FeedingLog::count());
        $this->assertEquals(10.25, (float) FeedingLog::first()->quantity_kg);
        $this->assertEquals(0.5, (float) FeedingLog::latest('id')->first()->quantity_kg);
        $this->assertSame($station->id, FeedingLog::first()->station_id);
        Carbon::setTestNow();
    }

    public function test_s2_has_four_sessions_and_rejects_duplicate_completion(): void
    {
        $worker = $this->worker();
        $station = $this->station('S2', 'Growing Station');
        StationFeedingConfig::create(['station_id' => $station->id, 'method' => 'four_times_daily', 'schedule_times' => ['07:00', '11:00', '15:00', '19:00']]);
        $batch = $this->batch($station);

        $this->assertCount(4, app(\App\Services\StationFeedingService::class)->schedulesFor($batch, Carbon::parse('2026-11-14')));
        $payload = ['feed_type' => 'Grower', 'quantity' => 5, 'unit' => 'kg', 'scheduled_at' => '2026-11-14 07:00:00'];
        $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), $payload)->assertRedirect();
        $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), $payload)->assertSessionHasErrors('scheduled_at');
        $this->assertSame(1, FeedingLog::count());
    }

    public function test_s3_uses_manager_configured_times(): void
    {
        $worker = $this->worker();
        $station = $this->station('S3', 'Finishing Station');
        StationFeedingConfig::create(['station_id' => $station->id, 'method' => 'twice_daily', 'schedule_times' => ['08:15', '18:30']]);
        $batch = $this->batch($station);

        foreach (['08:15', '18:30'] as $time) {
            $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), [
                'feed_type' => 'Finisher', 'quantity' => 2.5, 'unit' => 'kg', 'scheduled_at' => "2026-11-14 {$time}:00",
            ])->assertRedirect();
        }

        $this->assertSame(2, FeedingLog::count());
        $this->assertSame(['08:15', '18:30'], FeedingLog::orderBy('scheduled_at')->pluck('scheduled_at')->map(fn ($value) => $value->format('H:i'))->all());
    }

    public function test_feeding_history_keeps_station_snapshot_after_batch_transfer(): void
    {
        $worker = $this->worker();
        $s1 = $this->station('S1', 'Chick Station');
        $s2 = $this->station('S2', 'Growing Station');
        StationFeedingConfig::create(['station_id' => $s2->id, 'method' => 'four_times_daily', 'schedule_times' => ['07:00', '11:00', '15:00', '19:00']]);
        $batch = $this->batch($s1);

        $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), [
            'feed_type' => 'Starter', 'quantity' => 3, 'unit' => 'kg',
        ])->assertRedirect();

        $batch->update(['station_id' => $s2->id]);
        $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), [
            'feed_type' => 'Grower', 'quantity' => 4, 'unit' => 'kg', 'scheduled_at' => '2026-11-14 07:00:00',
        ])->assertRedirect();

        $this->assertSame([$s1->id, $s2->id], FeedingLog::orderBy('id')->pluck('station_id')->all());
        $this->assertEquals(7, (float) FeedingLog::sum('quantity_kg'));
    }

    public function test_monthly_consumption_can_be_grouped_by_station_and_batch(): void
    {
        $worker = $this->worker();
        $station = $this->station('S1', 'Chick Station');
        $batch = $this->batch($station, 'B-MONTH');

        foreach ([['2026-11-02 07:00:00', 3], ['2026-11-18 07:00:00', 4], ['2026-12-01 07:00:00', 9]] as [$timestamp, $quantity]) {
            Carbon::setTestNow($timestamp);
            $this->actingAs($worker)->post(route('worker.batches.feeding-logs.store', $batch), [
                'feed_type' => 'Starter', 'quantity' => $quantity, 'unit' => 'kg',
            ])->assertRedirect();
        }

        $november = FeedingLog::query()->where('station_id', $station->id)->where('batch_id', $batch->id)
            ->whereBetween('feeding_time', ['2026-11-01 00:00:00', '2026-11-30 23:59:59'])->sum('quantity_kg');
        $this->assertEquals(7, (float) $november);
        Carbon::setTestNow();
    }

    public function test_only_manager_can_change_station_feeding_configuration(): void
    {
        $station = $this->station('S3', 'Finishing Station');
        $payload = ['method' => 'twice_daily', 'schedule_times' => ['08:00', '18:00']];

        $this->actingAs($this->worker())->put(route('manager.stations.feeding-config.update', $station), $payload)->assertForbidden();
        $this->actingAs($this->manager())->put(route('manager.stations.feeding-config.update', $station), $payload)->assertRedirect();
        $this->assertSame(['08:00', '18:00'], $station->fresh()->feedingConfig->schedule_times);
    }

    public function test_workers_cannot_edit_or_delete_historical_feeding_quantity(): void
    {
        $worker = $this->worker();
        $station = $this->station('S1', 'Chick Station');
        $batch = $this->batch($station, 'B-POLICY');
        $log = FeedingLog::factory()->create(['user_id' => $worker->id, 'batch_id' => $batch->id, 'station_id' => $station->id]);
        $policy = new FeedingLogPolicy();

        $this->assertFalse($policy->update($worker, $log));
        $this->assertFalse($policy->delete($worker, $log));
    }
}
