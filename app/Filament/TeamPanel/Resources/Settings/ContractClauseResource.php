<?php

namespace App\Filament\TeamPanel\Resources\Settings;

use App\Filament\TeamPanel\Resources\Settings\ContractClause\Pages\CreateContractClause;
use App\Filament\TeamPanel\Resources\Settings\ContractClause\Pages\EditContractClause;
use App\Filament\TeamPanel\Resources\Settings\ContractClause\Pages\ListContractClauses;
use App\Models\ContractClause;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ContractClauseResource extends Resource
{
    protected static ?string $model = ContractClause::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Cláusulas de Contrato';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Textarea::make('content')
                ->label('Conteúdo')
                ->required()
                ->rows(6),
            TextInput::make('sort_order')
                ->label('Ordem')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('Ativa')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativa')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContractClauses::route('/'),
            'create' => CreateContractClause::route('/create'),
            'edit' => EditContractClause::route('/{record}/edit'),
        ];
    }
}
