<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('transaction_number')->nullable()->after('id');
            $table->foreignId('station_id')->nullable()->after('batch_id')->constrained()->nullOnDelete();
            $table->string('status', 20)->default('completed')->after('total_amount');
            $table->dateTime('cancelled_at')->nullable()->after('status');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');
        });

        foreach (DB::table('sales')->get() as $sale) {
            DB::table('sales')->where('id', $sale->id)->update([
                'transaction_number' => 'SALE-' . str_pad((string) $sale->id, 8, '0', STR_PAD_LEFT),
                'station_id' => DB::table('batches')->where('id', $sale->batch_id)->value('station_id'),
                'status' => 'completed',
            ]);
        }

        Schema::table('sales', function (Blueprint $table) {
            $table->unique('transaction_number');
            $table->index(['status', 'sale_date']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique(['transaction_number']);
            $table->dropIndex(['status', 'sale_date']);
            $table->dropForeign(['station_id']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['transaction_number', 'station_id', 'status', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
    }
};
