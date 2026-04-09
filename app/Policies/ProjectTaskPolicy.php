<?php

namespace App\Policies;

use App\Models\ProjectTask;
use App\Models\User;

class ProjectTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager', 'Developer', 'Designer']);
    }

    public function view(User $user, ProjectTask $task): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if ($user->hasRole('Project Manager')) {
            return $task->project?->project_manager_id === $user->id;
        }

        return $task->assigned_to === $user->id
            || (bool) $task->project?->members()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, ProjectTask $task): bool
    {
        return $this->updateStatus($user, $task);
    }

    public function updateStatus(User $user, ProjectTask $task): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if ($user->hasRole('Project Manager')) {
            return $task->project?->project_manager_id === $user->id;
        }

        return $task->assigned_to === $user->id;
    }
}