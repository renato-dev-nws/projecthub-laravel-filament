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
use Filament\Support\Enums\Width;
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
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Cyan,
                'gray'    => Color::Slate,
            ])
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('img/icon.svg'))
            ->navigationGroups([
                NavigationGroup::make('CRM'),
                NavigationGroup::make('Projetos'),
                NavigationGroup::make('Financeiro'),
                NavigationGroup::make('Configurações'),
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
            ->maxContentWidth(Width::Full)
            ->authGuard('web');
    }
}
