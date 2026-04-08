<?php

namespace App\Filament\TeamPanel\Clusters\Leads\Resources\Pages;

use App\Filament\TeamPanel\Clusters\Leads\Resources\LeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
}
