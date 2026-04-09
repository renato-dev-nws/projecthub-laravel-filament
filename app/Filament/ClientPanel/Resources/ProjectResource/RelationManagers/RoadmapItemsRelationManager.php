<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoadmapItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'roadmapItems';

    protected static ?string $title = 'Roadmap';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('is_public', true))
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('planned_date')
                    ->label('Data planejada')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('planned_date')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
