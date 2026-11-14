<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quantity_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->restrictOnDelete();
            $table->foreignId('manager_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('previous_quantity');
            $table->unsignedInteger('corrected_quantity');
            $table->text('reason');
            $table->timestamp('adjusted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quantity_adjustments');
    }
};
