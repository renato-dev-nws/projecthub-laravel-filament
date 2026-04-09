<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSupportTicketNotification extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'format' => 'filament',
            'title' => 'Novo ticket de suporte',
            'body' => "Ticket {$this->ticket->code}: {$this->ticket->subject}",
            'ticket_id' => $this->ticket->id,
            'ticket_edit_url' => "/admin/support/support-tickets/{$this->ticket->id}/edit",
        ];
    }
}
