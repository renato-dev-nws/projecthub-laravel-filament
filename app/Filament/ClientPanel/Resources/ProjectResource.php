<?php

namespace App\Filament\ClientPanel\Resources;

use App\Filament\ClientPanel\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Projetos';

    protected static ?string $modelLabel = 'Projeto';

    protected static ?string $pluralModelLabel = 'Projetos';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $clientId = auth('client_portal')->user()?->client_id;

        return parent::getEloquentQuery()
            ->where('client_id', $clientId)
            ->where('client_portal_enabled', true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label(''),

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Projeto')
                    ->weight('semibold')
                    ->searchable()
                    ->description(fn (Project $record) => $record->description),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'warning',
                        'on_hold'   => 'gray',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active'    => 'Ativo',
                        'planning'  => 'Planejamento',
                        'on_hold'   => 'Em Pausa',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('projectManager.name')
                    ->label('Gerente do Projeto')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->suffix('%')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 40 => 'warning',
                        default      => 'primary',
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Previsão de Entrega')
                    ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'planning'  => 'Planejamento',
                        'active'    => 'Ativo',
                        'on_hold'   => 'Em Pausa',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'view'  => Pages\ViewProject::route('/{record}'),
        ];
    }
}
