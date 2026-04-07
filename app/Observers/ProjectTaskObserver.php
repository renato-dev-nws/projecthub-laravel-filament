<?php

namespace App\Observers;

use App\Models\ProjectTask;

class ProjectTaskObserver
{
    public function created(ProjectTask $projectTask): void
    {
        $projectTask->project?->recalculateProgress();
    }

    public function updated(ProjectTask $projectTask): void
    {
        // Só recalcula se o status mudou
        if ($projectTask->wasChanged('status')) {
            $projectTask->project?->recalculateProgress();
        }
    }

    public function deleted(ProjectTask $projectTask): void
    {
        $projectTask->project?->recalculateProgress();
    }

    public function restored(ProjectTask $projectTask): void
    {
        $projectTask->project?->recalculateProgress();
    }
}
