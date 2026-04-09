<?php

namespace App\Filament\TeamPanel\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(''),
                TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Projeto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('client.company_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planning'  => 'info',
                        'active'    => 'success',
                        'on_hold'   => 'warning',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planning'  => 'Planejamento',
                        'active'    => 'Em Andamento',
                        'on_hold'   => 'Pausado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        default     => $state,
                    }),
                TextColumn::make('projectManager.name')
                    ->label('Gerente')
                    ->sortable(),
                TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->formatStateUsing(fn (int $state): string => "{$state}%")
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Entrega')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'planning'  => 'Planejamento',
                        'active'    => 'Em Andamento',
                        'on_hold'   => 'Pausado',
                        'completed' => 'Concluído',
                    ]),
                SelectFilter::make('project_manager_id')
                    ->label('Gerente')
                    ->relationship('projectManager', 'name'),
            ])
            ->recordActions([
                Action::make('contract')
                    ->label('Contrato')
                    ->icon('heroicon-o-document-text')
                    ->url(fn ($record) => route('projects.contract.pdf', $record))
                    ->openUrlInNewTab()
                    ->color('gray'),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
