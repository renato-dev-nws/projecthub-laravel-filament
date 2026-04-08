<?php

namespace App\Filament\TeamPanel\Resources\Settings\LeadSource\Pages;

use App\Filament\TeamPanel\Resources\Settings\LeadSourceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeadSource extends EditRecord
{
    protected static string $resource = LeadSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
