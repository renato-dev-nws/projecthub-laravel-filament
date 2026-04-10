<?php

namespace App\Filament\TeamPanel\Clusters\Leads;

use App\Models\User;
use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LeadsCluster extends Cluster
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $navigationLabel = 'Leads';

    protected static UnitEnum|string|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $clusterBreadcrumb = 'Leads';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->hasPermissionTo('module.crm')
            && $user->hasPermissionTo('leads.view_any');
    }
}
