<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\Pages;

use App\Filament\ClientPanel\Resources\ProjectResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Projeto')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')
                            ->label('Código')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('status')
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

                        TextEntry::make('progress_percent')
                            ->label('Progresso')
                            ->suffix('%')
                            ->badge()
                            ->color(fn (int $state) => match (true) {
                                $state >= 80 => 'success',
                                $state >= 40 => 'warning',
                                default      => 'primary',
                            }),

                        TextEntry::make('projectManager.name')
                            ->label('Gerente do Projeto')
                            ->icon('heroicon-m-user'),

                        TextEntry::make('start_date')
                            ->label('Data de Início')
                            ->date('d/m/Y'),

                        TextEntry::make('end_date')
                            ->label('Previsão de Entrega')
                            ->date('d/m/Y'),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ]),

                Section::make('Fases do Projeto')
                    ->schema([
                        TextEntry::make('phases_summary')
                            ->label('')
                            ->state(function ($record) {
                                $phases = $record->phases()->orderBy('sort_order')->get();
                                if ($phases->isEmpty()) {
                                    return 'Nenhuma fase cadastrada.';
                                }
                                $labels = [
                                    'planned'     => 'Planejada',
                                    'in_progress' => 'Em Andamento',
                                    'completed'   => 'Concluída',
                                    'cancelled'   => 'Cancelada',
                                    'pending'     => 'Pendente',
                                ];
                                return $phases->map(function ($phase) use ($labels) {
                                    $status = $labels[$phase->status] ?? $phase->status;
                                    $dates = ($phase->start_date && $phase->end_date)
                                        ? " ({$phase->start_date->format('d/m/Y')} → {$phase->end_date->format('d/m/Y')})"
                                        : '';
                                    return "• {$phase->name} — {$status}{$dates}";
                                })->join("\n");
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Tarefas')
                    ->schema([
                        TextEntry::make('tasks_summary')
                            ->label('')
                            ->state(function ($record) {
                                $tasks = $record->tasks()
                                    ->whereIn('status', ['todo', 'in_progress', 'review', 'done'])
                                    ->orderBy('due_date')
                                    ->get();
                                if ($tasks->isEmpty()) {
                                    return 'Nenhuma tarefa cadastrada.';
                                }
                                $labels = [
                                    'todo'        => 'A Fazer',
                                    'in_progress' => 'Em Progresso',
                                    'review'      => 'Em Revisão',
                                    'done'        => 'Concluída',
                                ];
                                return $tasks->map(function ($task) use ($labels) {
                                    $status = $labels[$task->status] ?? $task->status;
                                    $due = $task->due_date ? " — Prazo: {$task->due_date->format('d/m/Y')}" : '';
                                    return "• {$task->title} [{$status}]{$due}";
                                })->join("\n");
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
