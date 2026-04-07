<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager', 'Developer', 'Designer', 'Account Manager']);
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager'])) {
            return true;
        }

        if ($user->hasRole('Project Manager')) {
            return true; // vê todos para gestão
        }

        // Developer/Designer vê apenas projetos onde é membro
        return $project->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager']);
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        return $user->hasRole('Project Manager') && $project->project_manager_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('Super Admin');
    }
}
