<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->login()
            ->colors(['primary' => Color::Blue])
            ->brandName('Área do Cliente')
            ->viteTheme('resources/css/filament/client/theme.css')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->authGuard('client_portal')
            ->discoverResources(
                in: app_path('Filament/ClientPanel/Resources'),
                for: 'App\\Filament\\ClientPanel\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/ClientPanel/Pages'),
                for: 'App\\Filament\\ClientPanel\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/ClientPanel/Widgets'),
                for: 'App\\Filament\\ClientPanel\\Widgets'
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
