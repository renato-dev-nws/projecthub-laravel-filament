<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TeamPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
                'gray'    => Color::Slate,
            ])
            ->brandName('ProjectHub — Admin')
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make('CRM')->icon('heroicon-o-users'),
                NavigationGroup::make('Projetos')->icon('heroicon-o-folder'),
                NavigationGroup::make('Financeiro')->icon('heroicon-o-currency-dollar'),
                NavigationGroup::make('Configurações')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(
                in: app_path('Filament/TeamPanel/Resources'),
                for: 'App\\Filament\\TeamPanel\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TeamPanel/Pages'),
                for: 'App\\Filament\\TeamPanel\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/TeamPanel/Widgets'),
                for: 'App\\Filament\\TeamPanel\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class])
            ->authGuard('web');
            // ->plugins([
            //     \Filament\SpatieLaravelMediaLibraryPlugin\MediaLibraryPlugin::make(),
            //     \Filament\SpatieLaravelTagsPlugin\TagsPlugin::make(),
            // ]);
    }
}
