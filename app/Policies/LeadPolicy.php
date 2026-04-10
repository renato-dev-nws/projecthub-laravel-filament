<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('leads.view_any');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->hasPermissionTo('leads.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('leads.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        if (! $user->hasPermissionTo('leads.update')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager'])) {
            return true;
        }

        return $user->hasRole('Account Manager') && $lead->assigned_to === $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->hasPermissionTo('leads.delete');
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->hasPermissionTo('leads.delete');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->hasRole('Super Admin');
    }
}
