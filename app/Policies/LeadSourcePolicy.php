<?php

namespace App\Policies;

use App\Models\LeadSource;
use App\Models\User;

class LeadSourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function view(User $user, LeadSource $record): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function update(User $user, LeadSource $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function delete(User $user, LeadSource $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }
}
