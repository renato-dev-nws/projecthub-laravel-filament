<?php

namespace App\Filament\TeamPanel\Resources\Financial\FinancialTransaction\Pages;

use App\Filament\TeamPanel\Resources\Financial\FinancialTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinancialTransactions extends ListRecords
{
    protected static string $resource = FinancialTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
