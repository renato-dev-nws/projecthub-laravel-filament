<?php

namespace App\Filament\TeamPanel\Resources\Clients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados da Empresa')
                    ->schema([
                        Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'pessoa_juridica' => 'Pessoa Jurídica',
                                'pessoa_fisica'   => 'Pessoa Física',
                            ])
                            ->default('pessoa_juridica')
                            ->required()
                            ->live(),

                        TextInput::make('company_name')
                            ->label('Razão Social / Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('trade_name')
                            ->label('Nome Fantasia')
                            ->maxLength(255),

                        TextInput::make('cnpj')
                            ->label('CNPJ')
                            ->mask('99.999.999/9999-99')
                            ->maxLength(18),

                        TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->maxLength(14),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'prospect' => 'Prospect',
                                'active'   => 'Ativo',
                                'inactive' => 'Inativo',
                            ])
                            ->default('prospect')
                            ->required(),

                        Select::make('account_manager_id')
                            ->label('Gerente de Conta')
                            ->relationship('accountManager', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('Contato')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('industry')
                            ->label('Segmento')
                            ->maxLength(255),

                        TextInput::make('billing_email')
                            ->label('Email de Cobrança')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Endereço')
                    ->schema([
                        TextInput::make('address')
                            ->label('Endereço')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->label('Cidade')
                            ->maxLength(100),

                        TextInput::make('state')
                            ->label('Estado')
                            ->maxLength(2),

                        TextInput::make('postal_code')
                            ->label('CEP')
                            ->maxLength(9),

                        TextInput::make('country')
                            ->label('País')
                            ->default('BR')
                            ->maxLength(50),
                    ])->columns(2),

                Section::make('Contrato')
                    ->schema([
                        DatePicker::make('contract_start_date')
                            ->label('Início do Contrato'),
                        DatePicker::make('contract_end_date')
                            ->label('Fim do Contrato'),
                    ])->columns(2),

                Section::make('Observações')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}