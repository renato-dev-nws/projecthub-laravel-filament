<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\RelationManagers;

use App\Models\ClientPortalUser;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Comentários';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('content')
                    ->label('Mensagem')
                    ->required()
                    ->rows(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('is_internal', false))
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->label('Comentário')
                    ->limit(80)
                    ->searchable(),
                Tables\Columns\TextColumn::make('author_type')
                    ->label('Autor')
                    ->formatStateUsing(function ($state, $record): string {
                        if (($record->author_type ?? null) === ClientPortalUser::class) {
                            return 'Cliente';
                        }

                        return 'Equipe';
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Novo comentário')
                    ->modalHeading('Novo comentário')
                    ->modalSubmitActionLabel('Publicar comentário')
                    ->visible(fn (): bool => (bool) $this->ownerRecord?->client_can_comment)
                    ->mutateDataUsing(function (array $data): array {
                        $data['is_internal'] = false;
                        $data['author_type'] = ClientPortalUser::class;
                        $data['author_id'] = auth('client_portal')->id();

                        return $data;
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
