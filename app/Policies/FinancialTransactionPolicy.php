<?php

namespace App\Policies;

use App\Models\FinancialTransaction;
use App\Models\User;

class FinancialTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('financial.view_any');
    }

    public function view(User $user, FinancialTransaction $record): bool
    {
        return $user->hasPermissionTo('financial.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('financial.create');
    }

    public function update(User $user, FinancialTransaction $record): bool
    {
        return $user->hasPermissionTo('financial.update');
    }

    public function delete(User $user, FinancialTransaction $record): bool
    {
        return $user->hasPermissionTo('financial.delete');
    }
}
