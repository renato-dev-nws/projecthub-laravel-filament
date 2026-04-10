<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('quotes.view_any');
    }

    public function view(User $user, Quote $quote): bool
    {
        return $user->hasPermissionTo('quotes.view_any');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('quotes.create');
    }

    public function update(User $user, Quote $quote): bool
    {
        if (! $user->hasPermissionTo('quotes.update')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if ($user->hasRole('Account Manager')) {
            return $quote->status === 'draft' || $quote->created_by === $user->id;
        }

        return false;
    }

    public function delete(User $user, Quote $quote): bool
    {
        return $user->hasPermissionTo('quotes.delete');
    }

    public function restore(User $user, Quote $quote): bool
    {
        return $user->hasPermissionTo('quotes.delete');
    }

    public function forceDelete(User $user, Quote $quote): bool
    {
        return $user->hasRole('Super Admin');
    }
}
