<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('management_requests', function (Blueprint $table) {
            // Requests are an audit trail. A deleted batch may be submitted
            // again, so historical approved/rejected rows must not block it.
            // Duplicate pending requests are still prevented by the
            // application checks in the station and batch controllers.
            if (DB::getDriverName() === 'mysql') {
                $indexes = collect(DB::select('SHOW INDEX FROM management_requests'))
                    ->pluck('Key_name')
                    ->unique()
                    ->all();

                if (in_array('management_requests_request_type_request_key_status_unique', $indexes, true)) {
                    $table->dropUnique('management_requests_request_type_request_key_status_unique');
                }

                if (! in_array('management_requests_request_type_request_key_status_index', $indexes, true)) {
                    $table->index(['request_type', 'request_key', 'status']);
                }
            } else {
                $table->dropUnique('management_requests_request_type_request_key_status_unique');
                $table->index(['request_type', 'request_key', 'status']);
            }
        });
    }

    public function down(): void
    {
        // Existing history may contain repeated approved/rejected requests,
        // so restoring the old unique constraint is intentionally unsafe.
        Schema::table('management_requests', function (Blueprint $table) {
            $table->dropIndex('management_requests_request_type_request_key_status_index');
        });
    }
};
