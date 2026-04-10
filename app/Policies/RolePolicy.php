<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.view_any');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if (! $user->hasPermissionTo('roles.delete')) {
            return false;
        }

        return $role->name !== 'Super Admin';
    }
}
