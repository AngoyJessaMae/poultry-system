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
        Schema::create('feeding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('station_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('feeding_time_slot')->nullable();
            $table->string('feed_type');
            $table->decimal('quantity_kg', 8, 2);
            $table->dateTime('fed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeding_logs');
    }
};