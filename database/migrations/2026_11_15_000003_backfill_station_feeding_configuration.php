<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Some stations were created before station feeding configuration was
        // introduced. Give every existing station a persisted configuration.
        foreach (DB::table('stations')->get(['id', 'station_code']) as $station) {
            $defaults = match (strtoupper((string) $station->station_code)) {
                'S1' => ['method' => 'unlimited', 'times' => []],
                'S2' => ['method' => 'four_times_daily', 'times' => ['07:00', '11:00', '15:00', '19:00']],
                default => ['method' => 'twice_daily', 'times' => ['07:00', '19:00']],
            };

            DB::table('station_feeding_configs')->insertOrIgnore([
                'station_id' => $station->id,
                'method' => $defaults['method'],
                'schedule_times' => json_encode($defaults['times']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $method = DB::table('station_feeding_configs')
                ->where('station_id', $station->id)
                ->value('method') ?? $defaults['method'];

            // New batches already receive this value during validation. Align
            // legacy batches with the rule of their current station as well.
            DB::table('batches')
                ->where('station_id', $station->id)
                ->update(['feeding_method' => $method, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        // The backfill is intentionally not reversed: deleting these rows or
        // restoring old batch methods could make existing feeding records
        // inaccessible after a rollback.
    }
};
