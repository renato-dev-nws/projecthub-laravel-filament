<?php

namespace App\Filament\TeamPanel\Resources\Support\Pages;

use App\Filament\TeamPanel\Resources\Support\SupportTicketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupportTicket extends EditRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
