<?php

namespace App\Filament\TeamPanel\Resources\Financial\FinancialTransaction\Pages;

use App\Filament\TeamPanel\Resources\Financial\FinancialTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialTransaction extends EditRecord
{
    protected static string $resource = FinancialTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
