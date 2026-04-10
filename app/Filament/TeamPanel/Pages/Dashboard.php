<?php

namespace App\Filament\TeamPanel\Pages;

use App\Filament\TeamPanel\Widgets\LatestProjectsWidget;
use App\Filament\TeamPanel\Widgets\LeadsOverviewWidget;
use App\Filament\TeamPanel\Widgets\MonthlyFinanceChartWidget;
use App\Filament\TeamPanel\Widgets\PendingTasksWidget;
use App\Filament\TeamPanel\Widgets\SupportOverviewWidget;
use App\Filament\TeamPanel\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Dashboard extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && Gate::forUser($user)->allows('module.dashboard');
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            LatestProjectsWidget::class,
            PendingTasksWidget::class,
            LeadsOverviewWidget::class,
            SupportOverviewWidget::class,
            MonthlyFinanceChartWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 2,
            '2xl' => 3,
        ];
    }
}
