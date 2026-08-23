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
        Schema::create('mortality_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('station_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('mortality_date');
            $table->unsignedInteger('count');
            $table->string('suspected_cause')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortality_records');
    }
};