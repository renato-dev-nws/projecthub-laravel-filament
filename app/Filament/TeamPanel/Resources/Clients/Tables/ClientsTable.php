<?php

namespace App\Filament\TeamPanel\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('trade_name')
                    ->label('Nome Fantasia')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pessoa_juridica' => 'info',
                        'pessoa_fisica'   => 'warning',
                        default           => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pessoa_juridica' => 'PJ',
                        'pessoa_fisica'   => 'PF',
                        default           => $state,
                    }),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'prospect' => 'info',
                        'inactive' => 'gray',
                        'blocked'  => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active'   => 'Ativo',
                        'prospect' => 'Prospect',
                        'inactive' => 'Inativo',
                        'blocked'  => 'Bloqueado',
                        default    => $state,
                    }),
                TextColumn::make('accountManager.name')
                    ->label('Gerente')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'prospect' => 'Prospect',
                        'active'   => 'Ativo',
                        'inactive' => 'Inativo',
                        'blocked'  => 'Bloqueado',
                    ]),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'pessoa_juridica' => 'Pessoa Jurídica',
                        'pessoa_fisica'   => 'Pessoa Física',
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