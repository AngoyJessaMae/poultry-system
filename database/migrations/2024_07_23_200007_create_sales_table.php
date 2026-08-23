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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('sale_date');
            $table->unsignedInteger('heads_sold');
            $table->decimal('total_weight_kg', 8, 2);
            $table->decimal('price_per_kg', 8, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('buyer_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};