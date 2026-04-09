<?php

namespace App\Filament\TeamPanel\Resources\Support\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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
            Toggle::make('is_internal')
                ->label('Mensagem interna')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_type')
                    ->label('Autor')
                    ->formatStateUsing(fn ($state) => class_basename((string) $state)),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mensagem')
                    ->limit(80),
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Interna')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
