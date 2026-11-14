<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('category', 50);
            $table->decimal('amount', 12, 2);
            $table->date('income_date');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['income_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_incomes');
    }
};
