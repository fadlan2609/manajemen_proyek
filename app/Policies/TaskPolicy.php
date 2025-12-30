<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProjectManager() || $user->isMember();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->isAdmin() || 
               $task->project->project_manager_id === $user->id ||
               $task->assigned_to === $user->id ||
               $task->project->isMember($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin() || 
               $task->project->project_manager_id === $user->id ||
               $task->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() || $task->project->project_manager_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update task status (for members).
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $task->assigned_to === $user->id || 
               $user->isAdmin() || 
               $task->project->project_manager_id === $user->id;
    }

    /**
     * Determine whether the user can assign task to others.
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->isAdmin() || $task->project->project_manager_id === $user->id;
    }
}