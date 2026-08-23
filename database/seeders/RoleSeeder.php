<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Manager',
            'email' => 'manager@babiafarm.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        \App\Models\User::create([
            'name' => 'Worker',
            'email' => 'worker@babiafarm.test',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
    }
}