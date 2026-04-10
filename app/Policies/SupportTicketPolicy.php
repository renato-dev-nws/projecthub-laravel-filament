<?php

namespace App\Policies;

use App\Models\ClientPortalUser;
use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny($user): bool
    {
        if ($user instanceof User) {
            return $user->hasPermissionTo('support.view_any');
        }

        if ($user instanceof ClientPortalUser) {
            return true;
        }

        return false;
    }

    public function view($user, SupportTicket $ticket): bool
    {
        if ($user instanceof User) {
            if (! $user->hasPermissionTo('support.view_any')) {
                return false;
            }

            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager'])) {
                return true;
            }

            if ($user->hasRole('Project Manager') && $ticket->project_manager_id === $user->id) {
                return true;
            }

            return $ticket->assigned_to === $user->id || $ticket->project?->members()->where('users.id', $user->id)->exists();
        }

        if ($user instanceof ClientPortalUser) {
            return $ticket->client_id === $user->client_id;
        }

        return false;
    }

    public function create($user): bool
    {
        return $user instanceof User
            ? $user->hasPermissionTo('support.create')
            : $this->viewAny($user);
    }

    public function update($user, SupportTicket $ticket): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return $user->hasPermissionTo('support.update') && $this->view($user, $ticket);
    }

    public function delete($user, SupportTicket $ticket): bool
    {
        return $user instanceof User && $user->hasPermissionTo('support.delete');
    }
}
