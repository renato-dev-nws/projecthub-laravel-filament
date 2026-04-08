<?php

namespace App\Filament\TeamPanel\Clusters\Leads\Resources;

use App\Filament\TeamPanel\Clusters\Leads\LeadsCluster;
use App\Filament\TeamPanel\Clusters\Leads\Resources\Pages\CreateLead;
use App\Filament\TeamPanel\Clusters\Leads\Resources\Pages\EditLead;
use App\Filament\TeamPanel\Clusters\Leads\Resources\Pages\ListLeads;
use App\Filament\TeamPanel\Clusters\Leads\Resources\Schemas\LeadForm;
use App\Filament\TeamPanel\Clusters\Leads\Resources\Tables\LeadsTable;
use App\Models\Lead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $navigationLabel = 'Leads';

    protected static ?string $cluster = LeadsCluster::class;

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
