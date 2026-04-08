<?php

namespace App\Filament\TeamPanel\Resources\Clients\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';

    protected static ?string $title = 'Orçamentos';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Número')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->limit(40),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'          => 'success',
                        'sent', 'viewed'    => 'warning',
                        'rejected', 'expired' => 'danger',
                        default             => 'gray',
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->prefix('R$ ')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->date('d/m/Y'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.quotes.edit', $record)),
            ]);
    }
}
