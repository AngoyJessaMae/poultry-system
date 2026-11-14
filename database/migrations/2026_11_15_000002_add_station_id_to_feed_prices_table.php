<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_prices', function (Blueprint $table) {
            $table->foreignId('station_id')->nullable()->after('feed_type')->constrained('stations')->nullOnDelete();
            $table->index(['station_id', 'feed_type', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::table('feed_prices', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropIndex(['station_id', 'feed_type', 'effective_from']);
            $table->dropColumn('station_id');
        });
    }
};
