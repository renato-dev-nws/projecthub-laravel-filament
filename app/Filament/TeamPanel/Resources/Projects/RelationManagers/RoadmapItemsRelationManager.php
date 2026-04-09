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

class RoadmapItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'roadmapItems';

    protected static ?string $title = 'Roadmap';

    public function isReadOnly(): bool
    {
        return false;
    }

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
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'milestone' => 'Marco',
                        'deliverable' => 'Entrega',
                        'review' => 'Revisão',
                        'launch' => 'Lançamento',
                    ])
                    ->default('milestone')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Andamento',
                        'completed' => 'Concluído',
                        'delayed' => 'Atrasado',
                    ])
                    ->default('planned')
                    ->required(),
                DatePicker::make('planned_date')
                    ->label('Data Planejada')
                    ->required(),
                DatePicker::make('actual_date')
                    ->label('Data Real'),
                Toggle::make('is_public')
                    ->label('Visível no Portal do Cliente')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Ordem')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'milestone' => 'Marco',
                        'deliverable' => 'Entrega',
                        'review' => 'Revisão',
                        'launch' => 'Lançamento',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planned' => 'gray',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'delayed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Andamento',
                        'completed' => 'Concluído',
                        'delayed' => 'Atrasado',
                    }),
                Tables\Columns\TextColumn::make('planned_date')
                    ->label('Data Planejada')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean(),
            ])
            ->defaultSort('planned_date')
            ->filters([
                //
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
