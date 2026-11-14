<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_prices', function (Blueprint $table) {
            $table->id();
            $table->string('feed_type', 100);
            $table->decimal('price_per_sack', 12, 2);
            $table->decimal('sack_weight_kg', 10, 3)->default(50);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['feed_type', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_prices');
    }
};
