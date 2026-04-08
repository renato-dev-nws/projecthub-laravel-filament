<?php

namespace App\Filament\TeamPanel\Resources\Financial;

use App\Filament\TeamPanel\Resources\Financial\Bank\Pages\ListBanks;
use App\Models\Bank;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Bancos';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Banco';

    protected static ?string $pluralModelLabel = 'Bancos';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->maxLength(255),

            TextInput::make('code')
                ->label('Código do Banco')
                ->maxLength(10),

            TextInput::make('agency')
                ->label('Agência')
                ->maxLength(20),

            TextInput::make('account_number')
                ->label('Conta')
                ->maxLength(30),

            TextInput::make('balance')
                ->label('Saldo Inicial')
                ->numeric()
                ->prefix('R$')
                ->default(0),

            Toggle::make('is_active')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Código')
                    ->sortable(),

                TextColumn::make('balance')
                    ->label('Saldo')
                    ->prefix('R$ ')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanks::route('/'),
        ];
    }
}
