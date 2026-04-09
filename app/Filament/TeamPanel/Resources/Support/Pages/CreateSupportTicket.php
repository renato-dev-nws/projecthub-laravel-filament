<?php

namespace App\Filament\TeamPanel\Resources\Support\Pages;

use App\Filament\TeamPanel\Resources\Support\SupportTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;
}
