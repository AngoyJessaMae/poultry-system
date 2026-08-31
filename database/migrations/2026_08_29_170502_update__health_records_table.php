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
        Schema::table('health_records', function (Blueprint $table) {
            $table->renameColumn('recorded_at', 'recorded_date');
            $table->text('observation')->after('user_id');
            $table->string('medication_name')->nullable()->after('observation');
            $table->string('dosage_amount')->nullable()->after('medication_name');
            $table->string('dosage_unit')->nullable()->after('dosage_amount');
            $table->text('notes')->nullable()->after('dosage_unit');

            $table->dropColumn(['record_type', 'description', 'medication_given', 'dosage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->renameColumn('recorded_date', 'recorded_at');
            $table->dropColumn(['observation', 'medication_name', 'dosage_amount', 'dosage_unit', 'notes']);

            $table->enum('record_type', ['symptom', 'diagnosis', 'medication', 'recommendation'])->after('user_id');
            $table->text('description')->after('record_type');
            $table->string('medication_given')->nullable()->after('description');
            $table->string('dosage')->nullable()->after('medication_given');
        });
    }
};