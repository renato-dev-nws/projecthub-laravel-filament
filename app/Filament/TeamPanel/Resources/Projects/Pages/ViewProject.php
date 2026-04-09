<?php

namespace App\Filament\TeamPanel\Resources\Projects\Pages;

use App\Filament\TeamPanel\Resources\Projects\ProjectResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informações do Projeto')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')
                            ->label('Código')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('name')
                            ->label('Nome do Projeto')
                            ->columnSpan(2),

                        TextEntry::make('client.company_name')
                            ->label('Cliente'),

                        TextEntry::make('projectManager.name')
                            ->label('Gerente do Projeto')
                            ->icon('heroicon-m-user'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),

                        TextEntry::make('priority')
                            ->label('Prioridade')
                            ->badge(),

                        TextEntry::make('github_url')
                            ->label('Repositório GitHub')
                            ->url(fn ($state) => filled($state) ? $state : null)
                            ->openUrlInNewTab()
                            ->columnSpan(2),

                        TextEntry::make('progress_percent')
                            ->label('Progresso')
                            ->suffix('%')
                            ->badge(),

                        TextEntry::make('start_date')
                            ->label('Data de Início')
                            ->date('d/m/Y'),

                        TextEntry::make('end_date')
                            ->label('Previsão de Entrega')
                            ->date('d/m/Y'),

                        TextEntry::make('estimated_hours')
                            ->label('Horas Estimadas')
                            ->suffix('h'),

                        TextEntry::make('budget')
                            ->label('Orçamento')
                            ->money('BRL'),

                        TextEntry::make('spent')
                            ->label('Valor Gasto')
                            ->money('BRL'),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
