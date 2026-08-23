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
        Schema::create('growth_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('recorded_date');
            $table->unsignedInteger('age_days');
            $table->decimal('average_weight_grams', 8, 2);
            $table->enum('growth_stage', ['chick', 'grower', 'market_ready']);
            $table->boolean('is_below_expected')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growth_records');
    }
};