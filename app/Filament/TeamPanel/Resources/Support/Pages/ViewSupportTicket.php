<?php

namespace App\Filament\TeamPanel\Resources\Support\Pages;

use App\Filament\TeamPanel\Resources\Support\SupportTicketResource;
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
