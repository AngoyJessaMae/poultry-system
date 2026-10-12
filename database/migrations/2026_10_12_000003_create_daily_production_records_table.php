<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_production_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->date('recorded_date');
            $table->unsignedInteger('mortality_heads')->default(0);
            $table->decimal('actual_live_weight_grams', 10, 2)->nullable();
            $table->string('feed_type', 50)->nullable();
            $table->decimal('actual_feed_grams', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_production_records');
    }
};