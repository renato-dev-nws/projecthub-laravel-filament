<?php

namespace App\Filament\TeamPanel\Resources\Access;

use App\Filament\TeamPanel\Resources\Access\Role\Pages\CreateRole;
use App\Filament\TeamPanel\Resources\Access\Role\Pages\EditRole;
use App\Filament\TeamPanel\Resources\Access\Role\Pages\ListRoles;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Acesso';

    protected static ?string $navigationLabel = 'Funções';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Função';

    protected static ?string $pluralModelLabel = 'Funções';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('guard_name', 'web');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nome da Função')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('permissions')
                    ->label('Permissões')
                    ->relationship('permissions', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Função')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions.name')
                    ->label('Permissões')
                    ->badge()
                    ->separator(','),

                TextColumn::make('updated_at')
                    ->label('Atualizada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
