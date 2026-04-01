<?php

namespace App\Filament\TeamPanel\Resources\Quotes;

use App\Filament\TeamPanel\Resources\Quotes\Pages\CreateQuote;
use App\Filament\TeamPanel\Resources\Quotes\Pages\EditQuote;
use App\Filament\TeamPanel\Resources\Quotes\Pages\ListQuotes;
use App\Filament\TeamPanel\Resources\Quotes\Schemas\QuoteForm;
use App\Filament\TeamPanel\Resources\Quotes\Tables\QuotesTable;
use App\Models\Quote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string | UnitEnum | null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Orçamentos';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Orçamento';

    protected static ?string $pluralModelLabel = 'Orçamentos';

    public static function form(Schema $schema): Schema
    {
        return QuoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListQuotes::route('/'),
            'create' => CreateQuote::route('/create'),
            'edit'   => EditQuote::route('/{record}/edit'),
        ];
    }
}