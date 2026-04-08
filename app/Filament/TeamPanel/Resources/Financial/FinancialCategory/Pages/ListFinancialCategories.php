<?php

namespace App\Filament\TeamPanel\Resources\Financial\FinancialCategory\Pages;

use App\Filament\TeamPanel\Resources\Financial\FinancialCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinancialCategories extends ListRecords
{
    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
