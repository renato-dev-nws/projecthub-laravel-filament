<?php

namespace App\Filament\TeamPanel\Resources\Projects\Pages;

use App\Filament\TeamPanel\Actions\GenerateRoadmapAction;
use App\Filament\TeamPanel\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            GenerateRoadmapAction::make(),
            DeleteAction::make(),
        ];
    }
}
