<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClientProjectInteractionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Project $project,
        public string $action,
        public string $authorName,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nova interação do cliente',
            'body' => "{$this->authorName} {$this->action} no projeto {$this->project->code} - {$this->project->name}.",
            'project_id' => $this->project->id,
            'project_edit_url' => "/admin/projects/{$this->project->id}/edit",
        ];
    }
}
