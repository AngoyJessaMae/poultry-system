<?php

use App\Models\GrowthRecord;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        GrowthRecord::query()
            ->with('batch:id,arrival_date')
            ->chunkById(100, function ($records): void {
                foreach ($records as $record) {
                    $record->forceFill([
                        'age_days' => max(1, $record->batch->arrival_date->diffInDays($record->recorded_date) + 1),
                    ])->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        GrowthRecord::query()
            ->with('batch:id,arrival_date')
            ->chunkById(100, function ($records): void {
                foreach ($records as $record) {
                    $record->forceFill([
                        'age_days' => max(0, $record->batch->arrival_date->diffInDays($record->recorded_date)),
                    ])->saveQuietly();
                }
            });
    }
};