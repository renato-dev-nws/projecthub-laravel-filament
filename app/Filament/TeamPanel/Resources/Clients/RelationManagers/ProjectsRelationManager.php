<?php

namespace App\Filament\TeamPanel\Resources\Clients\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projetos';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Projeto')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'warning',
                        'on_hold'   => 'danger',
                        'completed' => 'info',
                        default     => 'gray',
                    }),
                TextColumn::make('projectManager.name')
                    ->label('Gerente'),
                TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->suffix('%'),
                TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y'),
                TextColumn::make('end_date')
                    ->label('Entrega')
                    ->date('d/m/Y'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.projects.edit', $record)),
            ]);
    }
}
