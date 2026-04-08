<?php

namespace App\Filament\TeamPanel\Resources\Services;

use App\Filament\TeamPanel\Resources\Services\Pages\CreateService;
use App\Filament\TeamPanel\Resources\Services\Pages\EditService;
use App\Filament\TeamPanel\Resources\Services\Pages\ListServices;
use App\Filament\TeamPanel\Resources\Services\Schemas\ServiceForm;
use App\Filament\TeamPanel\Resources\Services\Tables\ServicesTable;
use App\Models\Service;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string | UnitEnum | null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Serviços';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Serviço';

    protected static ?string $pluralModelLabel = 'Serviços';

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PricingTiersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit'   => EditService::route('/{record}/edit'),
        ];
    }
}