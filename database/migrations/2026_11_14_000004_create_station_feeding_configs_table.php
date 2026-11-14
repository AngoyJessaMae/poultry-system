<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_feeding_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('method', 30);
            $table->json('schedule_times')->nullable();
            $table->timestamps();
        });

        $now = now();
        foreach (DB::table('stations')->whereIn('station_code', ['S1', 'S2', 'S3'])->get() as $station) {
            $method = $station->station_code === 'S1' ? 'unlimited' : ($station->station_code === 'S2' ? 'four_times_daily' : 'twice_daily');
            $times = $station->station_code === 'S1' ? [] : ($station->station_code === 'S2' ? ['07:00', '11:00', '15:00', '19:00'] : []);
            DB::table('station_feeding_configs')->insert([
                'station_id' => $station->id,
                'method' => $method,
                'schedule_times' => json_encode($times),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('station_feeding_configs');
    }
};
