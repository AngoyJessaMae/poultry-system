<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('feeding_schedules')->delete();

        Schema::table('feeding_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('feeding_schedules', 'station_id')) {
                $table->dropForeign(['station_id']);
                $table->dropColumn('station_id');
            }
            if (!Schema::hasColumn('feeding_schedules', 'batch_id')) {
                $table->foreignId('batch_id')->after('id')->constrained()->onDelete('cascade');
            }
        });

        Schema::table('stations', function (Blueprint $table) {
            if (Schema::hasColumn('stations', 'feeding_method')) {
                $table->dropColumn('feeding_method');
            }
        });

        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'feeding_method')) {
                $table->string('feeding_method')->default('unlimited')->after('station_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('feeding_schedules', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            if (!Schema::hasColumn('feeding_schedules', 'station_id')) {
                $table->foreignId('station_id')->constrained()->onDelete('cascade');
            }
        });

        Schema::table('stations', function (Blueprint $table) {
            if (!Schema::hasColumn('stations', 'feeding_method')) {
                $table->string('feeding_method')->default('unlimited');
            }
        });

        Schema::table('batches', function (Blueprint $table) {
            if (Schema::hasColumn('batches', 'feeding_method')) {
                $table->dropColumn('feeding_method');
            }
        });
    }
};