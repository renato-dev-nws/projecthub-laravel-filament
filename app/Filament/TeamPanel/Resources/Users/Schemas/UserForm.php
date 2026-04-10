<?php

namespace App\Filament\TeamPanel\Resources\Users\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Dados do Usuário')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('E-mail')
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

                    TextInput::make('avatar_url')
                        ->label('URL do Avatar')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('Telefone')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('city')
                        ->label('Cidade')
                        ->maxLength(120),

                    TextInput::make('department')
                        ->label('Departamento')
                        ->maxLength(255),

                    TextInput::make('position')
                        ->label('Cargo Principal')
                        ->maxLength(255),

                    TagsInput::make('job_titles')
                        ->label('Cargos (tags)')
                        ->placeholder('Digite e pressione Enter')
                        ->columnSpanFull(),

                    TagsInput::make('skills')
                        ->label('Skills (tags)')
                        ->placeholder('Digite e pressione Enter')
                        ->columnSpanFull(),

                    TextInput::make('linkedin_url')
                        ->label('LinkedIn')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('github_url')
                        ->label('GitHub')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('portfolio_url')
                        ->label('Portfólio')
                        ->url()
                        ->maxLength(255),

                    Textarea::make('bio')
                        ->label('Mini bio')
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Usuário ativo')
                        ->default(true)
                        ->columnSpanFull(),
                ]),

            Section::make('Permissões')
                ->schema([
                    Select::make('roles')
                        ->label('Funções')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload(),
                ]),
        ]);
    }
}
