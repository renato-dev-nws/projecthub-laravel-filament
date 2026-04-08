<?php

namespace App\Filament\TeamPanel\Resources\Settings\LeadSource\Pages;

use App\Filament\TeamPanel\Resources\Settings\LeadSourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadSources extends ListRecords
{
    protected static string $resource = LeadSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
