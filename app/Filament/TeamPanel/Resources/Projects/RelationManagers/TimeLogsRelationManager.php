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
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TimeLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeLogs';

    protected static ?string $title = 'Registro de Horas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('task_id')
                    ->label('Tarefa')
                    ->relationship('task', 'title')
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3),
                TextInput::make('hours')
                    ->label('Horas')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.25)
                    ->suffix('h'),
                DatePicker::make('logged_date')
                    ->label('Data')
                    ->required()
                    ->default(now()),
                Toggle::make('is_billable')
                    ->label('Faturável')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable(),
                Tables\Columns\TextColumn::make('task.title')
                    ->label('Tarefa')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('hours')
                    ->label('Horas')
                    ->suffix('h')
                    ->sortable(),
                Tables\Columns\TextColumn::make('logged_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_billable')
                    ->label('Faturável')
                    ->boolean(),
            ])
            ->defaultSort('logged_date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('billable')
                    ->label('Apenas faturáveis')
                    ->query(fn ($query) => $query->where('is_billable', true)),
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
