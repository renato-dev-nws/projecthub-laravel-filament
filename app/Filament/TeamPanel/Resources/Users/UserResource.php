<?php

namespace App\Filament\TeamPanel\Resources\Users;

use App\Filament\TeamPanel\Resources\Users\Pages\CreateUser;
use App\Filament\TeamPanel\Resources\Users\Pages\EditUser;
use App\Filament\TeamPanel\Resources\Users\Pages\ListUsers;
use App\Filament\TeamPanel\Resources\Users\Schemas\UserForm;
use App\Filament\TeamPanel\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | UnitEnum | null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}