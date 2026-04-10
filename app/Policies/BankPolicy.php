<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;

class BankPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('financial.view_any');
    }

    public function view(User $user, Bank $record): bool
    {
        return $user->hasPermissionTo('financial.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('financial.create');
    }

    public function update(User $user, Bank $record): bool
    {
        return $user->hasPermissionTo('financial.update');
    }

    public function delete(User $user, Bank $record): bool
    {
        return $user->hasPermissionTo('financial.delete');
    }
}
