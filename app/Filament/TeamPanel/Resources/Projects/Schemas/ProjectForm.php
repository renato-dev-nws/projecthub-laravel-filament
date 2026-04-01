<?php

namespace App\Filament\TeamPanel\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('client_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quote_id')
                    ->numeric(),
                TextInput::make('project_manager_id')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('planning'),
                TextInput::make('priority')
                    ->required()
                    ->default('medium'),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                TextInput::make('estimated_hours')
                    ->numeric(),
                TextInput::make('logged_hours')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('budget')
                    ->numeric(),
                TextInput::make('spent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('progress_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('settings'),
                Toggle::make('client_portal_enabled')
                    ->required(),
                Toggle::make('client_can_comment')
                    ->required(),
                TextInput::make('color')
                    ->required()
                    ->default('#6366f1'),
            ]);
    }
}
