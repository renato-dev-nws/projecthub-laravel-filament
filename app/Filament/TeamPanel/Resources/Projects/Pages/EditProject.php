<?php

namespace App\Filament\TeamPanel\Resources\Projects\Pages;

use App\Filament\TeamPanel\Actions\GenerateRoadmapAction;
use App\Filament\TeamPanel\Resources\Projects\ProjectResource;
use App\Notifications\AddedToProjectNotification;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected array $existingMemberIds = [];

    protected function beforeSave(): void
    {
        $this->existingMemberIds = $this->record->members()->pluck('users.id')->all();
    }

    protected function afterSave(): void
    {
        $currentIds = $this->record->members()->pluck('users.id')->all();
        $addedIds = array_values(array_diff($currentIds, $this->existingMemberIds));

        if ($addedIds === []) {
            return;
        }

        $this->record->members()
            ->whereIn('users.id', $addedIds)
            ->get()
            ->each(fn ($user) => $user->notify(new AddedToProjectNotification($this->record)));
    }

    protected function getHeaderActions(): array
    {
        return [
            GenerateRoadmapAction::make(),
            DeleteAction::make(),
        ];
    }
}
