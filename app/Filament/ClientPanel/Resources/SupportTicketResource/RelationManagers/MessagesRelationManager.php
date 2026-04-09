<?php

namespace App\Filament\ClientPanel\Resources\SupportTicketResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Mensagens';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('message')
                ->label('Mensagem')
                ->required()
                ->rows(4),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('is_internal', false))
            ->columns([
                Tables\Columns\TextColumn::make('author_type')
                    ->label('Autor')
                    ->formatStateUsing(fn ($state) => class_basename((string) $state)),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mensagem')
                    ->limit(80),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
