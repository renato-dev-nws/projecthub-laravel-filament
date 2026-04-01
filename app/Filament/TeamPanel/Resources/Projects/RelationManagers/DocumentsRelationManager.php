<?php

namespace App\Filament\TeamPanel\Resources\Projects\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documentos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'markdown' => 'Markdown',
                        'file' => 'Arquivo',
                        'link' => 'Link Externo',
                    ])
                    ->default('markdown')
                    ->reactive()
                    ->required(),
                RichEditor::make('content')
                    ->label('Conteúdo')
                    ->visible(fn ($get) => $get('type') === 'markdown'),
                FileUpload::make('file_path')
                    ->label('Arquivo')
                    ->visible(fn ($get) => $get('type') === 'file'),
                TextInput::make('external_url')
                    ->label('URL Externa')
                    ->url()
                    ->visible(fn ($get) => $get('type') === 'link'),
                Select::make('visibility')
                    ->label('Visibilidade')
                    ->options([
                        'team' => 'Apenas Equipe',
                        'client' => 'Cliente',
                        'public' => 'Público',
                    ])
                    ->default('team')
                    ->required(),
                TextInput::make('category')
                    ->label('Categoria')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'markdown' => 'Markdown',
                        'file' => 'Arquivo',
                        'link' => 'Link',
                    }),
                Tables\Columns\TextColumn::make('visibility')
                    ->label('Visibilidade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'team' => 'gray',
                        'client' => 'info',
                        'public' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'team' => 'Equipe',
                        'client' => 'Cliente',
                        'public' => 'Público',
                    }),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->label('Versão'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
