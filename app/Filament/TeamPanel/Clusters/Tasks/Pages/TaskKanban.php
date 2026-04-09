<?php

namespace App\Filament\TeamPanel\Clusters\Tasks\Pages;

use App\Models\ProjectTask;

class TaskKanban extends AbstractTasksPage
{
    protected static ?string $navigationLabel = 'Kanban';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Kanban de Tarefas';

    public array $boards = [];

    public function getView(): string
    {
        return 'filament.team-panel.pages.tasks.task-kanban';
    }

    protected function refreshPageData(): void
    {
        $tasks = $this->taskQuery()->get();

        $this->boards = collect(ProjectTask::STATUS_LABELS)
            ->mapWithKeys(fn (string $label, string $status) => [
                $status => [
                    'label' => $label,
                    'tasks' => $tasks
                        ->where('status', $status)
                        ->map(fn ($task) => $this->mapTask($task))
                        ->values()
                        ->all(),
                ],
            ])
            ->all();
    }
}