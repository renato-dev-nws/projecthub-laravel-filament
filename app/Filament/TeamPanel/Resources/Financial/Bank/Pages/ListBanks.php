<?php

namespace App\Filament\TeamPanel\Resources\Financial\Bank\Pages;

use App\Filament\TeamPanel\Resources\Financial\BankResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBanks extends ListRecords
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
