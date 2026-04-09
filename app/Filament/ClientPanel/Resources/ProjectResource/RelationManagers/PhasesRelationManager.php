<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PhasesRelationManager extends RelationManager
{
    protected static string $relationship = 'phases';

    protected static ?string $title = 'Fases';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Fase')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fim')
                    ->date('d/m/Y'),
            ])
            ->defaultSort('sort_order')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
