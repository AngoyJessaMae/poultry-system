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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('record_type', ['symptom', 'diagnosis', 'medication', 'recommendation']);
            $table->text('description');
            $table->string('medication_given')->nullable();
            $table->string('dosage')->nullable();
            $table->dateTime('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};