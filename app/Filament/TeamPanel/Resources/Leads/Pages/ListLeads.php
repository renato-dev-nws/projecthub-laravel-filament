<?php

namespace App\Filament\TeamPanel\Resources\Leads\Pages;

use App\Filament\TeamPanel\Resources\Leads\LeadResource;
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
