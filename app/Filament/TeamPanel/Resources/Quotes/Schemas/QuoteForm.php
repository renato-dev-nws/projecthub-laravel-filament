<?php

namespace App\Filament\TeamPanel\Resources\Quotes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Orçamento')
                    ->schema([
                        TextInput::make('number')
                            ->label('Número')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('lead_id')
                            ->label('Lead Relacionado')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('created_by')
                            ->label('Criado por')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft'    => 'Rascunho',
                                'sent'     => 'Enviado',
                                'approved' => 'Aprovado',
                                'rejected' => 'Rejeitado',
                                'expired'  => 'Expirado',
                            ])
                            ->default('draft')
                            ->required(),

                        DatePicker::make('valid_until')
                            ->label('Válido até'),
                    ])->columns(2),

                Section::make('Resumo Financeiro')
                    ->schema([
                        TextInput::make('discount_value')
                            ->label('Desconto')
                            ->numeric()
                            ->default(0)
                            ->prefix('R$'),
                        TextInput::make('tax_value')
                            ->label('Impostos')
                            ->numeric()
                            ->default(0)
                            ->prefix('R$'),
                    ])->columns(2),

                Section::make('Itens do Orçamento')
                    ->schema([
                        Repeater::make('items')
                            ->label('Itens')
                            ->relationship('items')
                            ->schema([
                                Select::make('service_id')
                                    ->label('Serviço')
                                    ->relationship('service', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('description')
                                    ->label('Descrição')
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label('Quantidade')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                TextInput::make('unit_price')
                                    ->label('Preço Unitário')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Condições e Observações')
                    ->schema([
                        Textarea::make('internal_notes')
                            ->label('Observações Internas')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('terms_conditions')
                            ->label('Termos e Condições')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}