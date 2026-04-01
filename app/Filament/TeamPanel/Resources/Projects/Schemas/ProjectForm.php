<?php

namespace App\Filament\TeamPanel\Resources\Projects\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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

                        Select::make('quote_id')
                            ->label('Orçamento')
                            ->relationship('quote', 'number')
                            ->searchable()
                            ->preload(),

                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('company_name')->label('Empresa')->required(),
                                TextInput::make('email')->label('Email')->email(),
                            ]),

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
                    ])->columns(2),

                Section::make('Datas e Orçamento')
                    ->schema([
                        DatePicker::make('start_date')->label('Data de Início'),
                        DatePicker::make('end_date')->label('Previsão de Entrega')->after('start_date'),
                        TextInput::make('estimated_hours')->label('Horas Estimadas')->numeric()->suffix('h'),
                        TextInput::make('budget')->label('Orçamento')->numeric()->prefix('R$'),
                    ])->columns(2),

                Section::make('Equipe do Projeto')
                    ->schema([
                        Select::make('members')
                            ->label('Membros da Equipe')
                            ->relationship('members', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Descrição')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Descrição do Projeto')
                            ->columnSpanFull(),
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
                    ])->columns(3),
            ]);
    }
}
