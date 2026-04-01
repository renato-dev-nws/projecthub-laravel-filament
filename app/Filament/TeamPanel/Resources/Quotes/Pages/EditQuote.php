<?php

namespace App\Filament\TeamPanel\Resources\Quotes\Pages;

use App\Filament\TeamPanel\Resources\Quotes\QuoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
