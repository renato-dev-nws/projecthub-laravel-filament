<?php

namespace App\Filament\TeamPanel\Resources\Leads\Pages;

use App\Filament\TeamPanel\Resources\Leads\LeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
}
