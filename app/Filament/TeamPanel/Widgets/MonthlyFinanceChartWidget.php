<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\FinancialTransaction;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MonthlyFinanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Financeiro: Receitas x Despesas (6 meses)';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->hasPermissionTo('module.finance')
            && $user->hasPermissionTo('financial.view_any');
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset))
            ->push(now()->startOfMonth())
            ->values();

        $labels = $months->map(fn ($month) => ucfirst($month->translatedFormat('M/Y')))->all();

        $incomes = $months->map(function ($month) {
            return (float) FinancialTransaction::query()
                ->where('type', 'income')
                ->where('status', '!=', 'cancelled')
                ->whereYear('due_date', $month->year)
                ->whereMonth('due_date', $month->month)
                ->sum('amount');
        })->all();

        $expenses = $months->map(function ($month) {
            return (float) FinancialTransaction::query()
                ->where('type', 'expense')
                ->where('status', '!=', 'cancelled')
                ->whereYear('due_date', $month->year)
                ->whereMonth('due_date', $month->month)
                ->sum('amount');
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'data' => $incomes,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.25)',
                ],
                [
                    'label' => 'Despesas',
                    'data' => $expenses,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.25)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
