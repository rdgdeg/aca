<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AcaStatsWidget;
use App\Http\Middleware\ForceFrenchAdminLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('ACA Ath')
            ->login()
            ->profile()
            ->brandLogo(asset('images/logo-aca.jpg'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('images/logo-aca.jpg'))
            ->font('Montserrat')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17.5rem')
            ->maxContentWidth(Width::SevenExtraLarge)
            ->colors([
                'primary' => Color::hex('#6B2B91'),
                'warning' => Color::hex('#F0C400'),
                'gray' => Color::Stone,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'info' => Color::Violet,
            ])
            ->navigationGroups([
                NavigationGroup::make('Annuaire'),
                NavigationGroup::make('Centre-ville'),
                NavigationGroup::make('Messages'),
                NavigationGroup::make('Site'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AcaStatsWidget::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ForceFrenchAdminLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                function (): HtmlString {
                    $links = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';

                    try {
                        $links .= '<link rel="stylesheet" href="'.e(Vite::asset('resources/css/filament/admin/theme.css')).'">';
                    } catch (\Throwable) {
                        // Vite manifest is optional during tests or incomplete builds.
                    }

                    return new HtmlString($links);
                }
            )
            ->renderHook(
                PanelsRenderHook::SCRIPTS_AFTER,
                fn (): HtmlString => new HtmlString('<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>')
            );
    }
}
