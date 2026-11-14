<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $stationIds = DB::table('stations')
            ->where('station_code', 'S3')
            ->pluck('id');

        foreach ($stationIds as $stationId) {
            DB::table('station_feeding_configs')->updateOrInsert(
                ['station_id' => $stationId],
                [
                    'method' => 'twice_daily',
                    'schedule_times' => json_encode(['07:00', '19:00']),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        $stationIds = DB::table('stations')
            ->where('station_code', 'S3')
            ->pluck('id');

        DB::table('station_feeding_configs')
            ->whereIn('station_id', $stationIds)
            ->where('method', 'twice_daily')
            ->where('schedule_times', json_encode(['07:00', '19:00']))
            ->delete();
    }
};
