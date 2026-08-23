<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Station;
use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\HealthRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Enums\BatchStatus;
use App\Enums\FeedType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Manager and Worker users
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        $worker = User::factory()->create([
            'name' => 'Worker User',
            'email' => 'worker@example.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);


    }
}