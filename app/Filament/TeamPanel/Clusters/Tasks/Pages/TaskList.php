<?php

namespace App\Filament\TeamPanel\Clusters\Tasks\Pages;

class TaskList extends AbstractTasksPage
{
    protected static ?string $navigationLabel = 'Lista';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Tarefas';

    public array $tasks = [];

    public function getView(): string
    {
        return 'filament.team-panel.pages.tasks.task-list';
    }

    protected function refreshPageData(): void
    {
        $this->tasks = $this->taskQuery()
            ->get()
            ->map(fn ($task) => $this->mapTask($task))
            ->all();
    }
}