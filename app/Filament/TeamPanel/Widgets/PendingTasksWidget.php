<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\ProjectTask;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingTasksWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Tarefas Pendentes e Atrasadas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProjectTask::query()
                    ->whereIn('status', ['todo', 'in_progress', 'review'])
                    ->with(['project', 'assignee'])
                    ->whereNotNull('due_date')
                    ->orderBy('due_date')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tarefa')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn (ProjectTask $record) => $record->project?->name)
                    ->limit(50),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'todo'        => 'gray',
                        'in_progress' => 'warning',
                        'review'      => 'primary',
                        'done'        => 'success',
                        'blocked'     => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'todo'        => 'A Fazer',
                        'in_progress' => 'Em Progresso',
                        'review'      => 'Em Revisão',
                        'done'        => 'Concluída',
                        'blocked'     => 'Bloqueada',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'primary',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'urgent' => 'Urgente',
                        'high'   => 'Alta',
                        'medium' => 'Média',
                        'low'    => 'Baixa',
                        default  => $state,
                    }),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Responsável')
                    ->icon('heroicon-m-user')
                    ->default('—'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Prazo')
                    ->date('d/m/Y')
                    ->color(fn (ProjectTask $record) => $record->due_date?->isPast() ? 'danger' : 'success'),
            ])
            ->paginated(false);
    }
}
