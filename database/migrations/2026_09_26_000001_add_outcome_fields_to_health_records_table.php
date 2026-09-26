<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->unsignedInteger('affected_count')->default(1)->after('user_id');
            $table->unsignedInteger('dead_count')->default(0)->after('affected_count');
            $table->string('status')->default('under_treatment')->after('dead_count');
            $table->text('remarks')->nullable()->after('notes');
            $table->text('remedy')->nullable()->after('remarks');
            $table->foreignId('mortality_record_id')->nullable()->after('remedy')->constrained('mortality_records')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign(['mortality_record_id']);
            $table->dropColumn([
                'affected_count',
                'dead_count',
                'status',
                'remarks',
                'remedy',
                'mortality_record_id',
            ]);
        });
    }
};
