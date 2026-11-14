<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\ManagementRequest;
use App\Models\Station;
use App\Models\User;
use App\Notifications\ManagementRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase2ManagementApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_station_identifier_is_rejected(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        Station::create(['station_code' => 'S1', 'name' => 'Chick Station', 'capacity' => 100, 'min_age_days' => 1, 'max_age_days' => 10]);

        $this->actingAs($worker)->post(route('worker.stations.store'), [
            'station_code' => 's1', 'name' => 'Another Chick Station', 'capacity' => 100,
            'min_age_days' => 1, 'max_age_days' => 10,
        ])->assertSessionHasErrors('station_code');

        $this->assertDatabaseCount('management_requests', 0);
    }

    public function test_worker_station_request_is_pending_and_not_operational(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($worker)->post(route('worker.stations.store'), [
            'station_code' => 'S4', 'name' => 'New Station', 'capacity' => 200,
            'min_age_days' => 31, 'max_age_days' => 45,
        ])->assertRedirect(route('worker.management-requests.index', absolute: false));

        $request = ManagementRequest::firstOrFail();
        $this->assertSame('pending', $request->status);
        $this->assertDatabaseMissing('stations', ['station_code' => 'S4']);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $manager->id,
            'type' => ManagementRequestSubmitted::class,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.management-requests.index'))
            ->assertOk()
            ->assertSee('data-testid="notification-count"', false)
            ->assertSee('>1</span>', false);
    }

    public function test_manager_can_approve_station_request_once(): void
    {
        [$worker, $manager] = $this->users();
        $request = ManagementRequest::create([
            'request_type' => 'station', 'request_key' => 's4', 'requested_by' => $worker->id,
            'status' => 'pending', 'payload' => [
                'station_code' => 'S4', 'name' => 'New Station', 'capacity' => 200,
                'min_age_days' => 31, 'max_age_days' => 45, 'description' => null,
            ],
        ]);

        $this->actingAs($manager)->post(route('manager.management-requests.approve', $request))
            ->assertRedirect();

        $this->assertDatabaseHas('stations', ['station_code' => 'S4', 'name' => 'New Station']);
        $this->assertDatabaseHas('management_requests', ['id' => $request->id, 'status' => 'approved', 'reviewed_by' => $manager->id]);

        $this->actingAs($manager)->post(route('manager.management-requests.approve', $request))->assertStatus(409);
        $this->assertDatabaseCount('stations', 1);
    }

    public function test_manager_can_reject_batch_request_without_creating_a_batch(): void
    {
        [$worker, $manager] = $this->users();
        $station = Station::create(['station_code' => 'S1', 'name' => 'Chick Station', 'capacity' => 500, 'min_age_days' => 1, 'max_age_days' => 10]);
        $request = ManagementRequest::create([
            'request_type' => 'batch', 'request_key' => 'batch-1', 'requested_by' => $worker->id,
            'status' => 'pending', 'payload' => [
                'batch_code' => 'BATCH-1', 'station_id' => $station->id,
                'arrival_date' => now()->toDateString(), 'initial_quantity' => 100,
                'initial_weight_grams' => 45, 'feeding_method' => 'twice_daily', 'status' => 'active',
                'farm_name' => null, 'flockman_id' => null,
            ],
        ]);

        $this->actingAs($manager)->post(route('manager.management-requests.reject', $request), [
            'decision_notes' => 'Capacity planning is not complete.',
        ])->assertRedirect();

        $this->assertDatabaseHas('management_requests', ['id' => $request->id, 'status' => 'rejected', 'reviewed_by' => $manager->id]);
        $this->assertDatabaseMissing('batches', ['batch_code' => 'BATCH-1']);
    }

    public function test_duplicate_pending_batch_request_is_rejected(): void
    {
        [$worker] = $this->users();
        $station = Station::create(['station_code' => 'S1', 'name' => 'Chick Station', 'capacity' => 500, 'min_age_days' => 1, 'max_age_days' => 10]);
        $payload = [
            'batch_code' => 'BATCH-2', 'station_id' => $station->id, 'arrival_date' => now()->toDateString(),
            'initial_quantity' => 100, 'initial_weight_grams' => 45, 'feeding_method' => 'twice_daily', 'status' => 'active',
        ];

        $this->actingAs($worker)->post(route('worker.batches.store'), $payload)->assertRedirect();
        $this->actingAs($worker)->post(route('worker.batches.store'), $payload)->assertSessionHasErrors('batch_code');
        $this->assertDatabaseCount('management_requests', 1);
    }

    public function test_duplicate_existing_batch_identifier_is_rejected(): void
    {
        [$worker] = $this->users();
        $station = Station::create(['station_code' => 'S1', 'name' => 'Chick Station', 'capacity' => 500, 'min_age_days' => 1, 'max_age_days' => 10]);
        Batch::create([
            'batch_code' => 'BATCH-EXISTING', 'station_id' => $station->id, 'created_by' => $worker->id,
            'arrival_date' => now()->toDateString(), 'initial_quantity' => 100,
            'initial_weight_grams' => 45, 'feeding_method' => 'twice_daily', 'status' => 'active',
        ]);

        $this->actingAs($worker)->post(route('worker.batches.store'), [
            'batch_code' => 'batch-existing', 'station_id' => $station->id,
            'arrival_date' => now()->toDateString(), 'initial_quantity' => 50,
            'initial_weight_grams' => 45, 'feeding_method' => 'twice_daily', 'status' => 'active',
        ])->assertSessionHasErrors('batch_code');

        $this->assertDatabaseCount('management_requests', 0);
    }

    private function users(): array
    {
        return [
            User::factory()->create(['role' => 'worker']),
            User::factory()->create(['role' => 'manager']),
        ];
    }
}
