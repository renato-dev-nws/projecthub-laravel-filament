<?php

namespace App\Observers;

use App\Models\SupportTicket;
use App\Notifications\NewSupportTicketNotification;
use App\Notifications\SupportDelegatedNotification;

class SupportTicketObserver
{
    public function created(SupportTicket $ticket): void
    {
        if ($ticket->projectManager) {
            $ticket->projectManager->notify(new NewSupportTicketNotification($ticket));
        }
    }

    public function updated(SupportTicket $ticket): void
    {
        if ($ticket->wasChanged('assigned_to') && $ticket->assignee) {
            $ticket->assignee->notify(new SupportDelegatedNotification($ticket));
        }
    }
}
