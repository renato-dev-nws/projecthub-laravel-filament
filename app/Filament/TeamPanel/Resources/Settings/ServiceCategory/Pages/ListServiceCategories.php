<?php

namespace App\Filament\TeamPanel\Resources\Settings\ServiceCategory\Pages;

use App\Filament\TeamPanel\Resources\Settings\ServiceCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceCategories extends ListRecords
{
    protected static string $resource = ServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
