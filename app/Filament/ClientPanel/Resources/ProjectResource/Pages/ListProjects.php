<?php

namespace App\Filament\ClientPanel\Resources\ProjectResource\Pages;

use App\Filament\ClientPanel\Resources\ProjectResource;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
