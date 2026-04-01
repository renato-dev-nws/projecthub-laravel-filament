<?php

namespace App\Filament\TeamPanel\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('trade_name'),
                TextInput::make('cnpj'),
                TextInput::make('cpf'),
                TextInput::make('type')
                    ->required()
                    ->default('pessoa_juridica'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('website')
                    ->url(),
                TextInput::make('industry'),
                TextInput::make('address'),
                TextInput::make('city'),
                TextInput::make('state'),
                TextInput::make('zip_code'),
                TextInput::make('country')
                    ->required()
                    ->default('BR'),
                TextInput::make('status')
                    ->required()
                    ->default('prospect'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('account_manager_id')
                    ->numeric(),
            ]);
    }
}
