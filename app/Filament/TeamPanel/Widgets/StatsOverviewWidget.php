<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectTask;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeProjects = Project::where('status', 'active')->count();
        $activeClients = Client::where('status', 'active')->count();
        $pendingTasks = ProjectTask::whereIn('status', ['todo', 'in_progress'])->count();
        $openLeads = Lead::whereNotIn('status', ['converted', 'lost'])->count();

        $completedThisMonth = Project::where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->count();

        $overdueTasks = ProjectTask::whereIn('status', ['todo', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();

        return [
            Stat::make('Projetos Ativos', $activeProjects)
                ->description('Em andamento')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary')
                ->chart([$activeProjects + 2, $activeProjects + 1, $activeProjects]),

            Stat::make('Clientes Ativos', $activeClients)
                ->description('Com contratos')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Tarefas Pendentes', $pendingTasks)
                ->description($overdueTasks > 0 ? "{$overdueTasks} atrasadas" : 'Nenhuma atrasada')
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'success'),

            Stat::make('Leads Abertos', $openLeads)
                ->description('Em negociação')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
        ];
    }
}
