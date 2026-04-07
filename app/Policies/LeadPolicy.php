<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager', 'Project Manager']);
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager', 'Project Manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager']);
    }

    public function update(User $user, Lead $lead): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        return $user->hasRole('Account Manager') && $lead->assigned_to === $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->hasRole('Super Admin');
    }
}
