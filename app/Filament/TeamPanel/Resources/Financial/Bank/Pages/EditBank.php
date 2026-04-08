<?php

namespace App\Filament\TeamPanel\Resources\Financial\Bank\Pages;

use App\Filament\TeamPanel\Resources\Financial\BankResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBank extends EditRecord
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
