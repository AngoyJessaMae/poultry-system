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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->unique();
            $table->foreignId('station_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->date('arrival_date');
            $table->unsignedInteger('initial_quantity');
            $table->unsignedInteger('current_quantity');
            $table->decimal('initial_weight_grams', 8, 2)->default(45.00);
            $table->enum('feeding_method', ['twice_daily', 'four_times_daily', 'unlimited']);
            $table->enum('status', ['active', 'sold_out', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};