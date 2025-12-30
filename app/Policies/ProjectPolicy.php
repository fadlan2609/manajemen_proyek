<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user yang login bisa melihat daftar project
        // (meski nanti difilter di controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin bisa lihat semua project
        if ($user->isAdmin()) {
            return true;
        }
        
        // Project manager bisa lihat project yang mereka manage
        if ($project->project_manager_id === $user->id) {
            return true;
        }
        
        // Member bisa lihat project yang mereka ikuti
        return $project->members->contains('id', $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin dan project manager yang bisa create project
        return $user->isAdmin() || $user->isProjectManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Admin bisa update semua project
        if ($user->isAdmin()) {
            return true;
        }
        
        // Project manager hanya bisa update project yang mereka manage
        return $project->project_manager_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Admin bisa delete semua project
        if ($user->isAdmin()) {
            return true;
        }
        
        // Project manager hanya bisa delete project yang mereka manage
        return $project->project_manager_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        // Hanya admin yang bisa restore
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        // Hanya admin yang bisa permanent delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can add members to the project.
     */
    public function addMember(User $user, Project $project): bool
    {
        return $this->update($user, $project); // Sama dengan permission update
    }

    /**
     * Determine whether the user can remove members from the project.
     */
    public function removeMember(User $user, Project $project): bool
    {
        return $this->update($user, $project); // Sama dengan permission update
    }
}