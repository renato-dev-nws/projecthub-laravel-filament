<?php

namespace App\Filament\ClientPanel\Resources\SupportTicketResource\Pages;

use App\Filament\ClientPanel\Resources\SupportTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $clientUser = auth('client_portal')->user();
        $data['client_id'] = $clientUser?->client_id;

        return $data;
    }
}
