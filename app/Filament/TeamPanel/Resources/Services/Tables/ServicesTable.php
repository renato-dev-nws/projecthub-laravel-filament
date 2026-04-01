<?php

namespace App\Filament\TeamPanel\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'development' => 'Desenvolvimento',
                        'design'      => 'Design',
                        'consulting'  => 'Consultoria',
                        'support'     => 'Suporte',
                        'training'    => 'Treinamento',
                        'other'       => 'Outro',
                        default       => $state ?? '-',
                    }),
                TextColumn::make('default_price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Cobrança')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'hourly' => 'Por Hora',
                        'fixed' => 'Fixo',
                        'monthly' => 'Mensal',
                        default => $state ?? '-',
                    }),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'development' => 'Desenvolvimento',
                        'design'      => 'Design',
                        'consulting'  => 'Consultoria',
                        'support'     => 'Suporte',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}