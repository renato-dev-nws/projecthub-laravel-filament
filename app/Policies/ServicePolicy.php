<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function view(User $user, Service $record): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function update(User $user, Service $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function delete(User $user, Service $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }
}
