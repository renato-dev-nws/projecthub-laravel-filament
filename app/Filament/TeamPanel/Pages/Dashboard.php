<?php

namespace App\Filament\TeamPanel\Pages;

use App\Filament\TeamPanel\Widgets\LatestProjectsWidget;
use App\Filament\TeamPanel\Widgets\PendingTasksWidget;
use App\Filament\TeamPanel\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            LatestProjectsWidget::class,
            PendingTasksWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
