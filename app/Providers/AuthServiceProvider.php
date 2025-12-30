<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Task::class => TaskPolicy::class,
        // Tambahkan policies lain di sini jika diperlukan
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate untuk check role admin
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        // Gate untuk check role project manager
        Gate::define('project-manager-access', function (User $user) {
            return $user->isProjectManager() || $user->isAdmin();
        });

        // Gate untuk check role member
        Gate::define('member-access', function (User $user) {
            return $user->isMember() || $user->isProjectManager() || $user->isAdmin();
        });

        // Gate untuk manage users (admin only)
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Gate untuk manage projects
        Gate::define('manage-projects', function (User $user) {
            return $user->isAdmin() || $user->isProjectManager();
        });

        // Gate untuk view project
        Gate::define('view-project', function (User $user, Project $project) {
            return $user->isAdmin() || 
                   $project->project_manager_id === $user->id || 
                   $project->isMember($user->id);
        });

        // Gate untuk update project
        Gate::define('update-project', function (User $user, Project $project) {
            return $user->isAdmin() || $project->project_manager_id === $user->id;
        });

        // Gate untuk delete project
        Gate::define('delete-project', function (User $user, Project $project) {
            return $user->isAdmin() || $project->project_manager_id === $user->id;
        });

        // Gate untuk manage tasks
        Gate::define('manage-tasks', function (User $user, Project $project = null) {
            if ($project) {
                return $user->isAdmin() || $project->project_manager_id === $user->id;
            }
            return $user->isAdmin() || $user->isProjectManager();
        });

        // Gate untuk update task (untuk member yang ditugaskan)
        Gate::define('update-task-status', function (User $user, Task $task) {
            return $user->isAdmin() || 
                   $task->project->project_manager_id === $user->id ||
                   $task->assigned_to === $user->id;
        });
    }
}