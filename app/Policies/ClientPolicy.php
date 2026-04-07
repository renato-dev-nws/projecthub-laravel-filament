<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager', 'Project Manager']);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager', 'Project Manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager']);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager']);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->hasRole('Super Admin');
    }
}
