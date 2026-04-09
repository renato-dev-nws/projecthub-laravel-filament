<?php

namespace App\Observers;

use App\Models\ClientPortalUser;
use App\Models\ProjectComment;
use App\Models\User;
use App\Notifications\ClientProjectInteractionNotification;

class ProjectCommentObserver
{
    public function created(ProjectComment $comment): void
    {
        if (($comment->author_type ?? null) !== ClientPortalUser::class) {
            return;
        }

        $project = $comment->project;
        if (! $project) {
            return;
        }

        $memberIds = $project->members()->pluck('users.id')->all();
        $targetIds = collect([$project->project_manager_id, ...$memberIds])->filter()->unique()->values();
        $targets = User::whereIn('id', $targetIds)->get();

        $authorName = $comment->author?->name ?? 'Cliente';

        foreach ($targets as $user) {
            $user->notify(new ClientProjectInteractionNotification($project, 'enviou um comentário', $authorName));
        }
    }
}
