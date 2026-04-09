<?php

namespace App\Filament\TeamPanel\Resources\Projects\Pages;

use App\Filament\TeamPanel\Resources\Projects\ProjectResource;
use App\Notifications\AddedToProjectNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        $this->record->members()->get()->each(function ($user): void {
            $user->notify(new AddedToProjectNotification($this->record));
        });
    }
}
