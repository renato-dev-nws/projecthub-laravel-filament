<?php

namespace App\Policies;

use App\Models\FinancialCategory;
use App\Models\User;

class FinancialCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('financial.view_any');
    }

    public function view(User $user, FinancialCategory $record): bool
    {
        return $user->hasPermissionTo('financial.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('financial.create');
    }

    public function update(User $user, FinancialCategory $record): bool
    {
        return $user->hasPermissionTo('financial.update');
    }

    public function delete(User $user, FinancialCategory $record): bool
    {
        return $user->hasPermissionTo('financial.delete');
    }
}
