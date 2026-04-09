<?php

namespace App\Filament\TeamPanel\Resources\Support;

use App\Filament\TeamPanel\Resources\Support\Pages\CreateSupportTicket;
use App\Filament\TeamPanel\Resources\Support\Pages\EditSupportTicket;
use App\Filament\TeamPanel\Resources\Support\Pages\ListSupportTickets;
use App\Filament\TeamPanel\Resources\Support\Pages\ViewSupportTicket;
use App\Filament\TeamPanel\Resources\Support\RelationManagers\MessagesRelationManager;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static string|UnitEnum|null $navigationGroup = 'Projetos';

    protected static ?string $navigationLabel = 'Suporte';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $pluralModelLabel = 'Tickets de Suporte';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Código')
                ->disabled()
                ->dehydrated(false),
            Select::make('project_id')
                ->label('Projeto')
                ->relationship('project', 'name')
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (! $state) {
                        return;
                    }

                    $project = \App\Models\Project::find($state);
                    $set('client_id', $project?->client_id);
                    $set('project_manager_id', $project?->project_manager_id);
                }),
            Select::make('client_id')
                ->label('Cliente')
                ->relationship('client', 'company_name')
                ->required(),
            TextInput::make('subject')
                ->label('Assunto')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Descrição')
                ->required()
                ->rows(4),
            Select::make('status')
                ->label('Status')
                ->options([
                    'open' => 'Aberto',
                    'in_progress' => 'Em andamento',
                    'waiting_client' => 'Aguardando cliente',
                    'resolved' => 'Resolvido',
                    'closed' => 'Fechado',
                ])
                ->default('open')
                ->required(),
            Select::make('priority')
                ->label('Prioridade')
                ->options([
                    'low' => 'Baixa',
                    'medium' => 'Média',
                    'high' => 'Alta',
                    'urgent' => 'Urgente',
                ])
                ->default('medium')
                ->required(),
            Select::make('assigned_to')
                ->label('Delegado para')
                ->relationship('assignee', 'name')
                ->searchable()
                ->preload(),
            Select::make('project_manager_id')
                ->label('Gerente do Projeto')
                ->relationship('projectManager', 'name')
                ->searchable()
                ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Assunto')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('project.code')
                    ->label('Projeto')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Responsável'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Abertura')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Aberto',
                        'in_progress' => 'Em andamento',
                        'waiting_client' => 'Aguardando cliente',
                        'resolved' => 'Resolvido',
                        'closed' => 'Fechado',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'view' => ViewSupportTicket::route('/{record}'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
