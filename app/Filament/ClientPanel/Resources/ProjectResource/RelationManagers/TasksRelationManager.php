<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Tarefas';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tarefa')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Responsável'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Prazo')
                    ->date('d/m/Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
