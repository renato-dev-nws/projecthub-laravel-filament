<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\ClientPortalUser;

class ProjectPolicy
{
    public function viewAny($user): bool
    {
        if ($user instanceof User) {
            return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager', 'Developer', 'Designer', 'Account Manager']);
        }

        if ($user instanceof ClientPortalUser) {
            return true; // Filtrado via getEloquentQuery no Resource
        }

        return false;
    }

    public function view($user, Project $project): bool
    {
        if ($user instanceof User) {
            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager'])) {
                return true;
            }

            if ($user->hasRole('Project Manager')) {
                return true; // vê todos para gestão
            }

            // Developer/Designer vê apenas projetos onde é membro
            return $project->members()->where('user_id', $user->id)->exists();
        }

        if ($user instanceof ClientPortalUser) {
            return $project->client_id === $user->client_id && $project->client_portal_enabled;
        }

        return false;
    }

    public function create($user): bool
    {
        if ($user instanceof User) {
            return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager']);
        }

        return false;
    }

    public function update($user, Project $project): bool
    {
        if ($user instanceof User) {
            if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
                return true;
            }

            return $user->hasRole('Project Manager') && $project->project_manager_id === $user->id;
        }

        return false;
    }

    public function delete($user, Project $project): bool
    {
        if ($user instanceof User) {
            return $user->hasAnyRole(['Super Admin', 'Admin']);
        }

        return false;
    }

    public function restore($user, Project $project): bool
    {
        if ($user instanceof User) {
            return $user->hasAnyRole(['Super Admin', 'Admin']);
        }

        return false;
    }

    public function forceDelete($user, Project $project): bool
    {
        if ($user instanceof User) {
            return $user->hasRole('Super Admin');
        }

        return false;
    }
}
