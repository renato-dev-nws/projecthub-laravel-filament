<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\RelationManagers;

use App\Models\ClientPortalUser;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
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

    public function isReadOnly(): bool
    {
        return false;
    }

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
                        'file' => 'Arquivo',
                        'link' => 'Link',
                    ])
                    ->default('file')
                    ->required()
                    ->live(),
                FileUpload::make('file_path')
                    ->label('Arquivo')
                    ->visible(fn ($get) => $get('type') === 'file')
                    ->directory('project-documents')
                    ->required(fn ($get) => $get('type') === 'file'),
                TextInput::make('external_url')
                    ->label('URL')
                    ->url()
                    ->visible(fn ($get) => $get('type') === 'link')
                    ->required(fn ($get) => $get('type') === 'link'),
                Select::make('visibility')
                    ->label('Visibilidade')
                    ->options([
                        'client' => 'Cliente e Equipe',
                        'public' => 'Público',
                    ])
                    ->default('client')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereIn('visibility', ['client', 'public']))
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),
                Tables\Columns\TextColumn::make('visibility')
                    ->label('Visibilidade')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Enviar documento')
                    ->modalHeading('Enviar documento')
                    ->modalSubmitActionLabel('Salvar documento')
                    ->mutateDataUsing(function (array $data): array {
                        $data['uploader_type'] = ClientPortalUser::class;
                        $data['uploader_id'] = auth('client_portal')->id();
                        $data['is_public'] = $data['visibility'] === 'public';

                        return $data;
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
