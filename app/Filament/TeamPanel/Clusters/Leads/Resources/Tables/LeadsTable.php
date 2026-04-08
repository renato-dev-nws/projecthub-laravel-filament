<?php

namespace App\Filament\TeamPanel\Clusters\Leads\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('company')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'           => 'gray',
                        'contacted'     => 'info',
                        'qualified'     => 'warning',
                        'proposal_sent' => 'primary',
                        'negotiation'   => 'success',
                        'converted'     => 'success',
                        'lost'          => 'danger',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new'           => 'Novo',
                        'contacted'     => 'Contactado',
                        'qualified'     => 'Qualificado',
                        'proposal_sent' => 'Proposta Enviada',
                        'negotiation'   => 'Negociação',
                        'converted'     => 'Convertido',
                        'lost'          => 'Perdido',
                        default         => $state,
                    }),
                TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                        default => $state,
                    }),
                TextColumn::make('estimated_value')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Responsável')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expected_close_date')
                    ->label('Fechamento')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new'           => 'Novo',
                        'contacted'     => 'Contactado',
                        'qualified'     => 'Qualificado',
                        'proposal_sent' => 'Proposta Enviada',
                        'negotiation'   => 'Negociação',
                    ]),
                SelectFilter::make('priority')
                    ->label('Prioridade')
                    ->options([
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
