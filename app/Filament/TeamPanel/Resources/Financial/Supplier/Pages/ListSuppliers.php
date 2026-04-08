<?php

namespace App\Filament\TeamPanel\Resources\Financial\Supplier\Pages;

use App\Filament\TeamPanel\Resources\Financial\SupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
