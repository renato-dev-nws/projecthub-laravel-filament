<?php

namespace App\Policies;

use App\Models\ServiceCategory;
use App\Models\User;

class ServiceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function view(User $user, ServiceCategory $record): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function update(User $user, ServiceCategory $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function delete(User $user, ServiceCategory $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }
}
