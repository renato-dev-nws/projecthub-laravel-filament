<?php

namespace App\Filament\TeamPanel\Resources\Leads\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('company'),
                TextInput::make('source'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                TextInput::make('priority')
                    ->required()
                    ->default('medium'),
                TextInput::make('estimated_value')
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('expected_close_date'),
                TextInput::make('assigned_to')
                    ->numeric(),
                TextInput::make('converted_client_id')
                    ->numeric(),
                DateTimePicker::make('converted_at'),
            ]);
    }
}
