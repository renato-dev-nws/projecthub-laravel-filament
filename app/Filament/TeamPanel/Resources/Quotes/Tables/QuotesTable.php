<?php

namespace App\Filament\TeamPanel\Resources\Quotes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Número')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.company_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lead.name')
                    ->label('Lead')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'    => 'gray',
                        'sent'     => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'expired'  => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'    => 'Rascunho',
                        'sent'     => 'Enviado',
                        'approved' => 'Aprovado',
                        'rejected' => 'Rejeitado',
                        'expired'  => 'Expirado',
                        default    => $state,
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('valid_until')
                    ->label('Válido até')
                    ->date('d/m/Y')
                    ->sortable(),
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
                        'draft'    => 'Rascunho',
                        'sent'     => 'Enviado',
                        'approved' => 'Aprovado',
                        'rejected' => 'Rejeitado',
                        'expired'  => 'Expirado',
                    ]),
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label('PDF')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedDocumentArrowDown)
                    ->url(fn (\App\Models\Quote $record) => route('quotes.pdf', $record))
                    ->openUrlInNewTab()
                    ->color('gray'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}