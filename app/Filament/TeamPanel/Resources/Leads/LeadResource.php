<?php

namespace App\Filament\TeamPanel\Resources\Leads;

use App\Filament\TeamPanel\Resources\Leads\Pages\CreateLead;
use App\Filament\TeamPanel\Resources\Leads\Pages\EditLead;
use App\Filament\TeamPanel\Resources\Leads\Pages\ListLeads;
use App\Filament\TeamPanel\Resources\Leads\Schemas\LeadForm;
use App\Filament\TeamPanel\Resources\Leads\Tables\LeadsTable;
use App\Models\Lead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = 'Leads';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Lead';

    protected static ?string $pluralModelLabel = 'Leads';

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit'   => EditLead::route('/{record}/edit'),
        ];
    }
}