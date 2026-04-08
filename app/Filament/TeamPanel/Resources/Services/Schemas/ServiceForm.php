<?php

namespace App\Filament\TeamPanel\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Dados do Serviço')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('Código')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Select::make('service_category_id')
                        ->label('Categoria')
                        ->relationship('serviceCategory', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            \Filament\Forms\Components\TextInput::make('name')->label('Nome')->required(),
                            \Filament\Forms\Components\ColorPicker::make('color')->label('Cor'),
                        ]),
                    TextInput::make('default_price')
                        ->label('Preço Padrão / Hora')
                        ->numeric()
                        ->prefix('R$'),
                    Select::make('type')
                        ->label('Tipo de Cobrança')
                        ->options([
                            'hourly'  => 'Por Hora',
                            'fixed'   => 'Preço Fixo',
                            'monthly' => 'Mensal',
                        ])
                        ->default('fixed'),
                    TextInput::make('unit_type')
                        ->label('Unidade')
                        ->maxLength(30),
                    Toggle::make('is_active')
                        ->label('Ativo')
                        ->default(true),
                ]),

            Section::make('Descrição')
                ->schema([
                    Textarea::make('description')
                        ->label('Descrição')
                        ->rows(8),
                ]),
        ]);
    }
}
