<?php

namespace App\Observers;

use App\Models\ClientPortalUser;
use App\Models\ProjectDocument;
use App\Models\User;
use App\Notifications\ClientProjectInteractionNotification;

class ProjectDocumentObserver
{
    public function created(ProjectDocument $document): void
    {
        if (($document->uploader_type ?? null) !== ClientPortalUser::class) {
            return;
        }

        $project = $document->project;
        if (! $project) {
            return;
        }

        $memberIds = $project->members()->pluck('users.id')->all();
        $targetIds = collect([$project->project_manager_id, ...$memberIds])->filter()->unique()->values();
        $targets = User::whereIn('id', $targetIds)->get();

        $authorName = $document->uploader?->name ?? 'Cliente';

        foreach ($targets as $user) {
            $user->notify(new ClientProjectInteractionNotification($project, 'enviou um documento', $authorName));
        }
    }
}
