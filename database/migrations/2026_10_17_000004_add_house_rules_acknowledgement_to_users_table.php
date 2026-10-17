<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('house_rules_revision')->nullable()->after('policy_accepted_at');
            $table->timestamp('house_rules_viewed_at')->nullable()->after('house_rules_revision');
            $table->timestamp('house_rules_acknowledged_at')->nullable()->after('house_rules_viewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'house_rules_revision',
                'house_rules_viewed_at',
                'house_rules_acknowledged_at',
            ]);
        });
    }
};