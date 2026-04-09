<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AddedToProjectNotification extends Notification
{
    use Queueable;

    public function __construct(public Project $project)
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
            'title' => 'Você foi incluído em um projeto',
            'body' => "Você foi adicionado ao projeto {$this->project->code} - {$this->project->name}.",
            'project_id' => $this->project->id,
            'project_edit_url' => "/admin/projects/{$this->project->id}/edit",
        ];
    }
}
