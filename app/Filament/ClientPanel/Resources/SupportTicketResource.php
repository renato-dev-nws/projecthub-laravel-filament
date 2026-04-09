<?php

namespace App\Filament\ClientPanel\Resources;

use App\Filament\ClientPanel\Resources\SupportTicketResource\Pages;
use App\Filament\ClientPanel\Resources\SupportTicketResource\RelationManagers\MessagesRelationManager;
use App\Models\Project;
use App\Models\SupportTicket;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'Suporte';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $clientId = auth('client_portal')->user()?->client_id;

        return parent::getEloquentQuery()->where('client_id', $clientId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Código')
                ->disabled()
                ->dehydrated(false),
            Select::make('project_id')
                ->label('Projeto')
                ->options(function () {
                    $clientId = auth('client_portal')->user()?->client_id;

                    return Project::query()
                        ->where('client_id', $clientId)
                        ->where('client_portal_enabled', true)
                        ->pluck('name', 'id');
                })
                ->required(),
            TextInput::make('subject')
                ->label('Assunto')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Descrição')
                ->required()
                ->rows(4),
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
                ->disabled(fn ($operation) => $operation === 'create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Assunto')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.code')
                    ->label('Projeto')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Abertura')
                    ->dateTime('d/m/Y H:i'),
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
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
