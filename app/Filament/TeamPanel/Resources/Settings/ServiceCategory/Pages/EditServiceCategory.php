<?php

namespace App\Filament\TeamPanel\Resources\Settings\ServiceCategory\Pages;

use App\Filament\TeamPanel\Resources\Settings\ServiceCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceCategory extends EditRecord
{
    protected static string $resource = ServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
