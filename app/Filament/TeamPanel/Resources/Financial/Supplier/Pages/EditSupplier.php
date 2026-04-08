<?php

namespace App\Filament\TeamPanel\Resources\Financial\Supplier\Pages;

use App\Filament\TeamPanel\Resources\Financial\SupplierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
