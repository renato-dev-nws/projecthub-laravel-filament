<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\Pages;

use App\Filament\ClientPanel\Resources\ProjectResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Projeto')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')
                            ->label('Código')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('status')
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

                        TextEntry::make('progress_percent')
                            ->label('Progresso')
                            ->suffix('%')
                            ->badge()
                            ->color(fn (int $state) => match (true) {
                                $state >= 80 => 'success',
                                $state >= 40 => 'warning',
                                default      => 'primary',
                            }),

                        TextEntry::make('projectManager.name')
                            ->label('Gerente do Projeto')
                            ->icon('heroicon-m-user'),

                        TextEntry::make('github_url')
                            ->label('GitHub')
                            ->url(fn ($state) => filled($state) ? $state : null)
                            ->openUrlInNewTab(),

                        TextEntry::make('start_date')
                            ->label('Data de Início')
                            ->date('d/m/Y'),

                        TextEntry::make('end_date')
                            ->label('Previsão de Entrega')
                            ->date('d/m/Y'),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
