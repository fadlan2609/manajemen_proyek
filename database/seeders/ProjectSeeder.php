<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Get users
        $admin = User::where('email', 'admin@company.com')->first();
        $pm = User::where('email', 'pm@company.com')->first();
        $john = User::where('email', 'john@company.com')->first();
        $jane = User::where('email', 'jane@company.com')->first();

        // Create Sample Projects
        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => 'Redesign company website with modern UI/UX',
            'project_manager_id' => $pm->id,
            'deadline' => now()->addMonths(2),
            'status' => 'active',
            'progress' => 65,
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Develop mobile application for iOS and Android',
            'project_manager_id' => $pm->id,
            'deadline' => now()->addMonths(3),
            'status' => 'active',
            'progress' => 30,
        ]);

        $project3 = Project::create([
            'name' => 'Database Migration',
            'description' => 'Migrate from MySQL to PostgreSQL',
            'project_manager_id' => $admin->id,
            'deadline' => now()->addMonth(),
            'status' => 'active',
            'progress' => 80,
        ]);

        // Assign members to projects
        $project1->members()->attach([$john->id, $jane->id]);
        $project2->members()->attach([$john->id]);
        $project3->members()->attach([$jane->id]);

        // Create tasks for project 1
        Task::create([
            'project_id' => $project1->id,
            'title' => 'Design Homepage',
            'description' => 'Create new homepage design',
            'assigned_to' => $john->id,
            'status' => 'done',
            'priority' => 'high',
            'deadline' => now()->addDays(7),
            'progress' => 100,
        ]);

        Task::create([
            'project_id' => $project1->id,
            'title' => 'Implement Contact Form',
            'description' => 'Add contact form with validation',
            'assigned_to' => $jane->id,
            'status' => 'in_progress',
            'priority' => 'medium',
            'deadline' => now()->addDays(14),
            'progress' => 70,
        ]);

        Task::create([
            'project_id' => $project1->id,
            'title' => 'Mobile Responsive Testing',
            'description' => 'Test website on mobile devices',
            'assigned_to' => $john->id,
            'status' => 'todo',
            'priority' => 'low',
            'deadline' => now()->addDays(21),
            'progress' => 0,
        ]);

        // Create tasks for project 2
        Task::create([
            'project_id' => $project2->id,
            'title' => 'API Design',
            'description' => 'Design REST API endpoints',
            'assigned_to' => $pm->id,
            'status' => 'in_progress',
            'priority' => 'high',
            'deadline' => now()->addDays(10),
            'progress' => 60,
        ]);
    }
}