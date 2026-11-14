<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->string('station_code', 50)->nullable()->unique()->after('id');
        });

        DB::table('stations')->whereNull('station_code')->where('name', 'Chick Station')->update(['station_code' => 'S1']);
        DB::table('stations')->whereNull('station_code')->where('name', 'Growing Station')->update(['station_code' => 'S2']);
        DB::table('stations')->whereNull('station_code')->where('name', 'Finishing Station')->update(['station_code' => 'S3']);
    }

    public function down(): void
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropUnique(['station_code']);
            $table->dropColumn('station_code');
        });
    }
};
