<?php

namespace App\Filament\ClientPanel\Resources\SupportTicketResource\Pages;

use App\Filament\ClientPanel\Resources\SupportTicketResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
