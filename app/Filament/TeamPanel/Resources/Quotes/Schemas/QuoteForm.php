<?php

namespace App\Filament\TeamPanel\Resources\Quotes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->required(),
                TextInput::make('client_id')
                    ->required()
                    ->numeric(),
                TextInput::make('lead_id')
                    ->numeric(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('currency')
                    ->required()
                    ->default('BRL'),
                DatePicker::make('valid_until'),
                Textarea::make('terms_conditions')
                    ->columnSpanFull(),
                Textarea::make('internal_notes')
                    ->columnSpanFull(),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('viewed_at'),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
