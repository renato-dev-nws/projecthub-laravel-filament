<?php

namespace App\Filament\TeamPanel\Resources\Projects\Schemas;

use App\Models\Quote;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Informações do Projeto')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome do Projeto')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $state, Set $set) =>
                            $set('slug', Str::slug($state))
                        ),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('Código')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),
                    Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'company_name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->createOptionForm([
                            Select::make('type')
                                ->label('Tipo de Cliente')
                                ->options([
                                    'pessoa_juridica' => 'Pessoa Jurídica (PJ)',
                                    'pessoa_fisica'   => 'Pessoa Física (PF)',
                                ])
                                ->default('pessoa_juridica')
                                ->live()
                                ->required(),
                            Select::make('_from_lead_id')
                                ->label('Puxar dados de um Lead')
                                ->options(
                                    \App\Models\Lead::whereNull('converted_client_id')
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (! $state) return;
                                    $lead = \App\Models\Lead::find($state);
                                    if ($lead) {
                                        $set('company_name', $lead->company ?? $lead->name);
                                        $set('email', $lead->email);
                                        $set('phone', $lead->phone);
                                        $set('type', 'pessoa_juridica');
                                    }
                                }),
                            TextInput::make('company_name')
                                ->label(fn (Get $get) => $get('type') === 'pessoa_fisica' ? 'Nome Completo' : 'Razão Social')
                                ->required(),
                            TextInput::make('cnpj')
                                ->label('CNPJ')
                                ->visible(fn (Get $get) => $get('type') === 'pessoa_juridica'),
                            TextInput::make('cpf')
                                ->label('CPF')
                                ->visible(fn (Get $get) => $get('type') === 'pessoa_fisica'),
                            TextInput::make('email')
                                ->label('E-mail')
                                ->email(),
                            TextInput::make('phone')
                                ->label('Telefone')
                                ->tel(),
                        ]),
                    Select::make('quote_id')
                        ->label('Orçamento')
                        ->options(function (Get $get) {
                            $clientId = $get('client_id');
                            if (! $clientId) return [];
                            return Quote::where('client_id', $clientId)
                                ->whereIn('status', ['approved', 'sent'])
                                ->orderByDesc('created_at')
                                ->pluck('title', 'id');
                        })
                        ->disabled(fn (Get $get) => ! $get('client_id'))
                        ->helperText(fn (Get $get) => ! $get('client_id')
                            ? 'Selecione um cliente primeiro para ver os orçamentos disponíveis'
                            : null)
                        ->searchable()
                        ->nullable(),
                    Select::make('project_manager_id')
                        ->label('Gerente do Projeto')
                        ->relationship('projectManager', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'planning'  => 'Planejamento',
                            'active'    => 'Em Andamento',
                            'on_hold'   => 'Pausado',
                            'completed' => 'Concluído',
                            'cancelled' => 'Cancelado',
                        ])
                        ->default('planning')
                        ->required(),
                    Select::make('priority')
                        ->label('Prioridade')
                        ->options([
                            'low'      => 'Baixa',
                            'medium'   => 'Média',
                            'high'     => 'Alta',
                            'critical' => 'Crítica',
                        ])
                        ->default('medium'),
                ]),

            Section::make('Datas e Orçamento')
                ->schema([
                    DatePicker::make('start_date')
                        ->label('Data de Início'),
                    DatePicker::make('end_date')
                        ->label('Previsão de Entrega')
                        ->after('start_date'),
                    TextInput::make('estimated_hours')
                        ->label('Horas Estimadas')
                        ->numeric()
                        ->suffix('h'),
                    TextInput::make('budget')
                        ->label('Orçamento')
                        ->numeric()
                        ->prefix('R$'),
                ]),

            Section::make('Equipe do Projeto')
                ->schema([
                    Select::make('members')
                        ->label('Membros da Equipe')
                        ->relationship('members', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Configurações do Portal')
                ->schema([
                    Toggle::make('client_portal_enabled')
                        ->label('Habilitar Portal do Cliente')
                        ->default(true),
                    Toggle::make('client_can_comment')
                        ->label('Permitir Comentários do Cliente')
                        ->default(true),
                    ColorPicker::make('color')
                        ->label('Cor do Projeto'),
                ]),

            Section::make('Descrição')
                ->schema([
                    RichEditor::make('description')
                        ->label('Descrição do Projeto'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
