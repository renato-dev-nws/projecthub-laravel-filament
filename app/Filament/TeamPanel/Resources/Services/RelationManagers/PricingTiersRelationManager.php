<?php

namespace App\Filament\TeamPanel\Resources\Services\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricingTiersRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingTiers';
    protected static ?string $title = 'Tabela de Preços por Horas';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('label')
                ->label('Rótulo')
                ->placeholder('Ex: Até 10h')
                ->maxLength(100)
                ->columnSpanFull(),
            TextInput::make('min_hours')
                ->label('Horas mínimas')
                ->numeric()
                ->suffix('h')
                ->required()
                ->default(0),
            TextInput::make('max_hours')
                ->label('Horas máximas')
                ->numeric()
                ->suffix('h')
                ->helperText('Deixe vazio para sem limite'),
            TextInput::make('price_per_hour')
                ->label('R$/hora')
                ->numeric()
                ->prefix('R$')
                ->required()
                ->default(0),
            TextInput::make('sort_order')
                ->label('Ordem')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')->label('Rótulo')->searchable(),
                TextColumn::make('min_hours')->label('Horas min.')->suffix('h'),
                TextColumn::make('max_hours')->label('Horas máx.')->suffix('h')->default('∞'),
                TextColumn::make('price_per_hour')->label('R$/hora')->prefix('R$ ')->numeric(decimalPlaces: 2),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
