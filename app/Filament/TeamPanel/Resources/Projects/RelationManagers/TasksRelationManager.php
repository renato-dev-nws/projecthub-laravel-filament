<?php

namespace App\Filament\TeamPanel\Resources\Projects\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Tarefas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('phase_id')
                    ->label('Fase')
                    ->relationship('phase', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'todo' => 'A Fazer',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'done' => 'Concluída',
                        'blocked' => 'Bloqueada',
                    ])
                    ->default('todo')
                    ->required(),
                Select::make('priority')
                    ->label('Prioridade')
                    ->options([
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                        'critical' => 'Crítica',
                    ])
                    ->default('medium')
                    ->required(),
                Select::make('assigned_to')
                    ->label('Atribuída a')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('due_date')
                    ->label('Data de Vencimento'),
                TextInput::make('estimated_hours')
                    ->label('Horas Estimadas')
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'info',
                        'review' => 'warning',
                        'done' => 'success',
                        'blocked' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo' => 'A Fazer',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'done' => 'Concluída',
                        'blocked' => 'Bloqueada',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                        'critical' => 'Crítica',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Atribuída a')
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'todo' => 'A Fazer',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'done' => 'Concluída',
                        'blocked' => 'Bloqueada',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
