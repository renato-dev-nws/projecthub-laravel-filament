<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('clients.view_any');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('clients.create');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.update');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.delete');
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.delete');
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->hasRole('Super Admin');
    }
}
