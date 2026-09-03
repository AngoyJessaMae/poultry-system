<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['feeding', 'growth', 'medication', 'mortality', 'sales', 'profit']);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->dateTime('generated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};