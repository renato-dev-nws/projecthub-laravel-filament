<?php

namespace App\Filament\TeamPanel\Resources\Quotes\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
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
                    Textarea::make('description')
                        ->label('Descricao')
                        ->rows(3)
                        ->columnSpanFull(),
                    Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'company_name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live(),
                    Select::make('lead_id')
                        ->label('Lead')
                        ->relationship('lead', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Nome')
                                ->required(),
                            TextInput::make('email')
                                ->label('E-mail')
                                ->email(),
                            Select::make('source')
                                ->label('Origem')
                                ->options([
                                    'website'  => 'Website',
                                    'referral' => 'Indicação',
                                    'other'    => 'Outro',
                                ])
                                ->default('other'),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return Lead::create(array_merge($data, ['status' => 'new']))->id;
                        }),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'    => 'Rascunho',
                            'sent'     => 'Enviado',
                            'viewed'   => 'Visualizado',
                            'approved' => 'Aprovado',
                            'rejected' => 'Rejeitado',
                            'expired'  => 'Expirado',
                        ])
                        ->default('draft')
                        ->required(),
                    DatePicker::make('valid_until')
                        ->label('Válido até'),
                ]),

            Section::make('Resumo Financeiro')
                ->schema([
                    TextInput::make('discount_value')
                        ->label('Desconto (R$)')
                        ->numeric()
                        ->default(0)
                        ->prefix('R$'),
                    TextInput::make('tax_value')
                        ->label('Impostos (R$)')
                        ->numeric()
                        ->default(0)
                        ->prefix('R$'),
                    Textarea::make('internal_notes')
                        ->label('Observações Internas')
                        ->rows(3),
                    Textarea::make('terms_conditions')
                        ->label('Termos e Condições')
                        ->rows(3),
                ]),

            Section::make('Fases do Projeto')
                ->schema([
                    Repeater::make('phases')
                        ->label('Fases')
                        ->relationship('phases')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nome da Fase')
                                ->required()
                                ->columnSpan(2),
                            DatePicker::make('deadline_date')
                                ->label('Prazo da Fase')
                                ->columnSpan(1),
                            TextInput::make('estimated_days')
                                ->label('Dias Estimados')
                                ->numeric()
                                ->suffix('dias')
                                ->columnSpan(1),
                            Textarea::make('description')
                                ->label('Descrição da Fase')
                                ->rows(2)
                                ->columnSpanFull(),

                            Repeater::make('items')
                                ->label('Itens da Fase')
                                ->relationship('items')
                                ->schema([
                                    Select::make('service_id')
                                        ->label('Serviço')
                                        ->relationship('service', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $service = \App\Models\Service::find($state);
                                                $set('unit_price', $service?->default_price ?? 0);
                                            }
                                        }),
                                    TextInput::make('description')
                                        ->label('Descrição')
                                        ->required(),
                                    TextInput::make('hours')
                                        ->label('Horas')
                                        ->numeric()
                                        ->suffix('h'),
                                    TextInput::make('unit_price')
                                        ->label('R$/hora')
                                        ->numeric()
                                        ->prefix('R$'),
                                ])
                                ->columns(4)
                                ->columnSpanFull(),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
