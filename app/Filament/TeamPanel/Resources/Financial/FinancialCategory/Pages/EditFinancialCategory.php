<?php

namespace App\Filament\TeamPanel\Resources\Financial\FinancialCategory\Pages;

use App\Filament\TeamPanel\Resources\Financial\FinancialCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialCategory extends EditRecord
{
    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
