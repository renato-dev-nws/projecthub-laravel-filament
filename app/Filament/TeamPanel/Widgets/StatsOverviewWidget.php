<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\Client;
use App\Models\FinancialTransaction;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user() !== null;
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return [];
        }

        $stats = [];

        if ($user->hasPermissionTo('module.projects') && $user->hasPermissionTo('projects.view_any')) {
            $activeProjects = $this->projectQueryFor($user)
                ->whereIn('status', ['active', 'planning', 'on_hold'])
                ->count();

            $stats[] = Stat::make('Projetos em Andamento', $activeProjects)
                ->description('Carteira visível para seu perfil')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary');
        }

        if ($user->hasPermissionTo('module.tasks') && $user->hasPermissionTo('tasks.view_any')) {
            $taskQuery = ProjectTask::query()->whereIn('status', ['todo', 'in_progress', 'review', 'blocked']);

            if ($user->hasRole('Project Manager')) {
                $taskQuery->whereHas('project', fn (Builder $builder) => $builder->where('project_manager_id', $user->id));
            } elseif (! $user->hasAnyRole(['Super Admin', 'Admin'])) {
                $taskQuery->where('assigned_to', $user->id);
            }

            $pendingTasks = (clone $taskQuery)->count();
            $overdueTasks = (clone $taskQuery)
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->count();

            $stats[] = Stat::make('Tarefas Abertas', $pendingTasks)
                ->description($overdueTasks > 0 ? "{$overdueTasks} atrasadas" : 'Sem atrasos críticos')
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'success');
        }

        if ($user->hasPermissionTo('module.crm')) {
            $activeClients = $user->hasPermissionTo('clients.view_any')
                ? Client::where('status', 'active')->count()
                : null;

            if ($activeClients !== null) {
                $stats[] = Stat::make('Clientes Ativos', $activeClients)
                    ->description('Base ativa com contrato')
                    ->descriptionIcon('heroicon-m-building-office')
                    ->color('success');
            }

            if ($user->hasPermissionTo('leads.view_any')) {
                $openLeads = Lead::whereNotIn('status', ['converted', 'lost'])->count();

                $stats[] = Stat::make('Leads Abertos', $openLeads)
                    ->description('Pipeline comercial')
                    ->descriptionIcon('heroicon-m-user-plus')
                    ->color('warning');
            }
        }

        if ($user->hasPermissionTo('module.support') && $user->hasPermissionTo('support.view_any')) {
            $supportOpen = SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_client'])->count();

            $stats[] = Stat::make('Tickets de Suporte', $supportOpen)
                ->description('Chamados em aberto')
                ->descriptionIcon('heroicon-m-lifebuoy')
                ->color('info');
        }

        if ($user->hasPermissionTo('module.finance') && $user->hasPermissionTo('financial.view_any')) {
            $pendingAmount = FinancialTransaction::where('status', 'pending')->sum('amount');

            $stats[] = Stat::make('Financeiro Pendente', 'R$ ' . number_format((float) $pendingAmount, 2, ',', '.'))
                ->description('Títulos pendentes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray');
        }

        return $stats;
    }

    private function projectQueryFor(User $user): Builder
    {
        $query = Project::query();

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Financial']) || $user->hasPermissionTo('projects.view_all')) {
            return $query;
        }

        if ($user->hasRole('Project Manager')) {
            return $query->where('project_manager_id', $user->id);
        }

        return $query->whereHas('members', fn (Builder $builder) => $builder->where('users.id', $user->id));
    }
}
