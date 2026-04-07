<?php

namespace App\Filament\ClientPanel\Widgets;

use App\Models\Project;
use App\Models\ProjectTask;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $clientId = auth('client_portal')->user()?->client_id;

        $activeProjects = Project::where('client_id', $clientId)
            ->whereIn('status', ['active', 'planning'])
            ->count();

        $completedProjects = Project::where('client_id', $clientId)
            ->where('status', 'completed')
            ->count();

        $pendingTasks = ProjectTask::whereHas('project', fn ($q) => $q->where('client_id', $clientId))
            ->whereIn('status', ['todo', 'in_progress'])
            ->count();

        $doneTasks = ProjectTask::whereHas('project', fn ($q) => $q->where('client_id', $clientId))
            ->where('status', 'done')
            ->count();

        return [
            Stat::make('Projetos Ativos', $activeProjects)
                ->description('Em andamento')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary'),

            Stat::make('Projetos Concluídos', $completedProjects)
                ->description('Entregues')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Tarefas em Aberto', $pendingTasks)
                ->description("{$doneTasks} concluídas")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),
        ];
    }
}
