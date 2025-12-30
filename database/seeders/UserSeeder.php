<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+6281234567890',
            'position' => 'System Administrator',
            'department' => 'IT Department',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Project Manager
        User::create([
            'name' => 'Project Manager',
            'email' => 'pm@company.com',
            'password' => Hash::make('password'),
            'role' => 'project_manager',
            'phone' => '+6289876543210',
            'position' => 'Senior Project Manager',
            'department' => 'Project Management Office',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Regular Members
        User::create([
            'name' => 'John Doe',
            'email' => 'john@company.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '+6281122334455',
            'position' => 'Software Developer',
            'department' => 'Engineering',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@company.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '+6285566778899',
            'position' => 'UI/UX Designer',
            'department' => 'Design',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Robert Johnson',
            'email' => 'robert@company.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '+6289900112233',
            'position' => 'Quality Assurance',
            'department' => 'Testing',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create more dummy users with different statuses
        User::factory(10)->create([
            'status' => 'active',
        ]);

        User::factory(3)->create([
            'status' => 'inactive',
        ]);

        User::factory(2)->create([
            'status' => 'suspended',
        ]);
    }
}