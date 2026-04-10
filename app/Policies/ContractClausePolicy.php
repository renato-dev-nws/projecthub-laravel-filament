<?php

namespace App\Policies;

use App\Models\ContractClause;
use App\Models\User;

class ContractClausePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function view(User $user, ContractClause $record): bool
    {
        return $user->hasPermissionTo('settings.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function update(User $user, ContractClause $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }

    public function delete(User $user, ContractClause $record): bool
    {
        return $user->hasPermissionTo('settings.update');
    }
}
