<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The legacy quantity column was limited to 9,999.99 grams. Keep it
        // as a compatibility field, but expand it so a valid kg entry cannot
        // overflow while quantity_kg remains the authoritative value.
        Schema::table('feeding_logs', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        Schema::table('feeding_logs', function (Blueprint $table) {
            $table->foreignId('station_id')->nullable()->after('batch_id')->constrained()->nullOnDelete();
            $table->decimal('quantity_kg', 10, 3)->nullable()->after('quantity');
            $table->string('unit', 10)->default('kg')->after('quantity_kg');
            $table->dateTime('scheduled_at')->nullable()->after('feeding_time');
            $table->text('notes')->nullable()->after('scheduled_at');
            $table->unique(['batch_id', 'scheduled_at']);
        });

        foreach (DB::table('feeding_logs')->get() as $log) {
            $stationId = DB::table('batches')->where('id', $log->batch_id)->value('station_id');
            DB::table('feeding_logs')->where('id', $log->id)->update([
                'station_id' => $stationId,
                'quantity_kg' => ((float) $log->quantity) / 1000,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('feeding_logs', function (Blueprint $table) {
            $table->dropUnique(['batch_id', 'scheduled_at']);
            $table->dropForeign(['station_id']);
            $table->dropColumn(['station_id', 'quantity_kg', 'unit', 'scheduled_at', 'notes']);
        });
    }
};
