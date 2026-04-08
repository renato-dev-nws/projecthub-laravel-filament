<?php

namespace App\Filament\TeamPanel\Clusters\Leads\Resources\Pages;

use App\Filament\TeamPanel\Clusters\Leads\Resources\LeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected static ?string $navigationLabel = 'Lista';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
