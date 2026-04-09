<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProjectsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Projetos Ativos';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->whereIn('status', ['active', 'planning'])
                    ->with(['client', 'projectManager'])
                    ->orderByDesc('updated_at')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('')
                    ->width('4px'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->color('gray')
                    ->width('90px'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Projeto')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (Project $record) => $record->client?->company_name),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'warning',
                        'on_hold'   => 'gray',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active'    => 'Ativo',
                        'planning'  => 'Planejamento',
                        'on_hold'   => 'Em Pausa',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('projectManager.name')
                    ->label('Gerente')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->suffix('%')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 40 => 'warning',
                        default      => 'danger',
                    }),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Prazo')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->end_date?->isPast() ? 'danger' : null),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Project $record) => route('filament.admin.resources.projects.view', $record)),
            ])
            ->paginated(false);
    }
}
