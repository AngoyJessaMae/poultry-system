<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('management_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_type', 30);
            $table->string('request_key');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->string('status', 20)->default('pending')->index();
            $table->json('payload');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamps();
            $table->unique(['request_type', 'request_key', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('management_requests');
    }
};
