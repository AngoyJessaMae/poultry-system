<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feeding_logs', function (Blueprint $table) {
            $table->foreignId('feeding_schedule_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeding_logs', function (Blueprint $table) {
            $table->dropForeign(['feeding_schedule_id']);
            $table->dropColumn('feeding_schedule_id');
        });
    }
};