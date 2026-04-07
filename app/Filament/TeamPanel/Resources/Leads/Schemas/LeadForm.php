<?php

namespace App\Filament\TeamPanel\Resources\Leads\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Dados do Lead')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label('Telefone')
                        ->tel()
                        ->maxLength(20),
                    TextInput::make('company')
                        ->label('Empresa')
                        ->maxLength(255),
                    TextInput::make('website')
                        ->label('Site do Lead')
                        ->url()
                        ->prefixIcon(Heroicon::OutlinedGlobeAlt)
                        ->maxLength(255),
                    TextInput::make('referral_url')
                        ->label('URL de Referência')
                        ->url()
                        ->prefixIcon(Heroicon::OutlinedLink)
                        ->maxLength(255),
                ]),

            Section::make('Qualificação')
                ->schema([
                    Select::make('source')
                        ->label('Origem')
                        ->options([
                            'website'   => 'Website',
                            'referral'  => 'Indicação',
                            'linkedin'  => 'LinkedIn',
                            'google'    => 'Google Ads',
                            'event'     => 'Evento',
                            'cold_call' => 'Cold Call',
                            'other'     => 'Outro',
                        ]),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'new'           => 'Novo',
                            'contacted'     => 'Contactado',
                            'qualified'     => 'Qualificado',
                            'proposal_sent' => 'Proposta Enviada',
                            'negotiation'   => 'Negociação',
                            'converted'     => 'Convertido',
                            'lost'          => 'Perdido',
                        ])
                        ->default('new')
                        ->required(),
                    Select::make('priority')
                        ->label('Prioridade')
                        ->options([
                            'low'    => 'Baixa',
                            'medium' => 'Média',
                            'high'   => 'Alta',
                        ])
                        ->default('medium'),
                    TextInput::make('estimated_value')
                        ->label('Valor Estimado')
                        ->numeric()
                        ->prefix('R$'),
                    DatePicker::make('expected_close_date')
                        ->label('Previsão de Fechamento'),
                    Select::make('assigned_to')
                        ->label('Responsável')
                        ->relationship('assignedTo', 'name')
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Descrição')
                ->schema([
                    Textarea::make('description')
                        ->label('Descrição / Necessidade')
                        ->rows(4),
                ])
                ->columnSpanFull(),
        ]);
    }
}
