<?php

namespace App\Filament\TeamPanel\Clusters\Leads\Resources\Pages;

use App\Filament\TeamPanel\Clusters\Leads\Resources\LeadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
