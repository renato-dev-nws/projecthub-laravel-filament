<?php

namespace App\Filament\TeamPanel\Resources\Settings\ContractClause\Pages;

use App\Filament\TeamPanel\Resources\Settings\ContractClauseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContractClauses extends ListRecords
{
    protected static string $resource = ContractClauseResource::class;

    protected static ?string $title = 'Cláusulas de Contrato';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nova Cláusula de Contrato'),
        ];
    }
}
