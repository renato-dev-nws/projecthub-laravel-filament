<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Quote;
use App\Models\TimeLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Projetos Ativos', Project::where('status', 'active')->count())
                ->description('Em execução agora')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Leads em Aberto', Lead::whereNotIn('status', ['converted', 'lost'])->count())
                ->description('Aguardando tratativa')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('warning'),

            Stat::make('Orçamentos Pendentes', Quote::where('status', 'sent')->count())
                ->description('Aguardando aprovação')
                ->color('info'),

            Stat::make('Horas no Mês',
                TimeLog::whereMonth('logged_date', now()->month)->sum('hours') . 'h'
            )
                ->description('Horas lançadas no mês atual')
                ->color('primary'),
        ];
    }
}
