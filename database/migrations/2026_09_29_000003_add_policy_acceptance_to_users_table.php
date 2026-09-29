<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('policy_revision')->nullable()->after('is_active');
            $table->timestamp('policy_viewed_at')->nullable()->after('policy_revision');
            $table->timestamp('policy_accepted_at')->nullable()->after('policy_viewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['policy_revision', 'policy_viewed_at', 'policy_accepted_at']);
        });
    }
};