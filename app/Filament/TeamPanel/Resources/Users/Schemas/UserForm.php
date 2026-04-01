<?php

namespace App\Filament\TeamPanel\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Usuário')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ])->columns(2),

                Section::make('Permissões')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload(),
                    ]),
            ]);
    }
}