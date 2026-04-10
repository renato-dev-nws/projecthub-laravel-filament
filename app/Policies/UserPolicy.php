<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view_any');
    }

    public function view(User $user, User $record): bool
    {
        return $user->hasPermissionTo('users.view_any') || $user->hasPermissionTo('projects.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    public function update(User $user, User $record): bool
    {
        return $user->hasPermissionTo('users.update');
    }

    public function delete(User $user, User $record): bool
    {
        if (! $user->hasPermissionTo('users.delete')) {
            return false;
        }

        return ! $record->hasRole('Super Admin');
    }

    public function restore(User $user, User $record): bool
    {
        return $this->delete($user, $record);
    }

    public function forceDelete(User $user, User $record): bool
    {
        return $user->hasRole('Super Admin');
    }
}
