<?php

namespace App\Filament\TeamPanel\Resources\Settings\ContractClause\Pages;

use App\Filament\TeamPanel\Resources\Settings\ContractClauseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContractClause extends EditRecord
{
    protected static string $resource = ContractClauseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
