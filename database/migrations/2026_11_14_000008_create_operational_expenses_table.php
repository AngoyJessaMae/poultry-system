<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('category', 50);
            $table->decimal('amount', 12, 2);
            $table->decimal('quantity', 12, 3)->nullable();
            $table->string('unit', 30)->nullable();
            $table->date('expense_date');
            $table->string('allocation_type', 20)->default('overall');
            $table->foreignId('station_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['expense_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_expenses');
    }
};
