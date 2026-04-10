<?php

namespace App\Filament\TeamPanel\Resources\Access\Role\Pages;

use App\Filament\TeamPanel\Resources\Access\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
