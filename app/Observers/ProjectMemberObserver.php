<?php

namespace App\Observers;

use App\Models\ProjectMember;
use App\Notifications\AddedToProjectNotification;

class ProjectMemberObserver
{
    public function created(ProjectMember $projectMember): void
    {
        $user = $projectMember->user;
        $project = $projectMember->project;

        if (! $user || ! $project) {
            return;
        }

        $user->notify(new AddedToProjectNotification($project));
    }
}
