<?php

namespace App\Filament\ClientPanel\Pages;

use App\Filament\ClientPanel\Widgets\ClientProjectsWidget;
use App\Filament\ClientPanel\Widgets\ClientStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Meus Projetos';

    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            ClientStatsWidget::class,
            ClientProjectsWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 3;
    }
}
