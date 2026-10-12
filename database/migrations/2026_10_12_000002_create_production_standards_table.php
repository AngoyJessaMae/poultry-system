<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_standards', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('age_days')->unique();
            $table->decimal('mortality_percent', 8, 4)->nullable();
            $table->decimal('live_weight_grams', 10, 2)->nullable();
            $table->decimal('feed_grams', 12, 2)->nullable();
            $table->decimal('fcr', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_standards');
    }
};