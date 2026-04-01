<?php

namespace App\Filament\ClientPanel\Resources\Projects\Tables;

use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(''),
                TextColumn::make('name')
                    ->label('Projeto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('projectManager.name')
                    ->label('Gerente')
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Entrega')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('budget')
                    ->label('Orçamento')
                    ->money('BRL')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
