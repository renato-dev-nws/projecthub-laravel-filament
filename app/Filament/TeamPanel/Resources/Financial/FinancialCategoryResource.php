<?php

namespace App\Filament\TeamPanel\Resources\Financial;

use App\Filament\TeamPanel\Resources\Financial\FinancialCategory\Pages\CreateFinancialCategory;
use App\Filament\TeamPanel\Resources\Financial\FinancialCategory\Pages\EditFinancialCategory;
use App\Filament\TeamPanel\Resources\Financial\FinancialCategory\Pages\ListFinancialCategories;
use App\Models\FinancialCategory;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FinancialCategoryResource extends Resource
{
    protected static ?string $model = FinancialCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Categorias';

    protected static ?int $navigationSort = 11;

    protected static ?string $modelLabel = 'Categoria';

    protected static ?string $pluralModelLabel = 'Categorias';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->label('Tipo')
                ->options([
                    'income'  => 'Receita',
                    'expense' => 'Despesa',
                ])
                ->default('expense')
                ->required(),

            ColorPicker::make('color')
                ->label('Cor'),

            Toggle::make('is_active')
                ->label('Ativo')
                ->default(true),

            TextInput::make('sort_order')
                ->label('Ordem')
                ->numeric(),
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

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income'  => 'success',
                        'expense' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'income'  => 'Receita',
                        'expense' => 'Despesa',
                        default   => $state,
                    }),

                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFinancialCategories::route('/'),
            'create' => CreateFinancialCategory::route('/create'),
            'edit'   => EditFinancialCategory::route('/{record}/edit'),
        ];
    }
}
