<?php

namespace App\Filament\TeamPanel\Resources\Settings\ContractClause\Pages;

use App\Filament\TeamPanel\Resources\Settings\ContractClauseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContractClauses extends ListRecords
{
    protected static string $resource = ContractClauseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
