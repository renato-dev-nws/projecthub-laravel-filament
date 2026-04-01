<?php

namespace App\Filament\TeamPanel\Resources\Quotes\Pages;

use App\Filament\TeamPanel\Resources\Quotes\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;
}
