<?php

namespace App\Providers\Filament;

use App\Filament\User\Pages\Dashboard as UserDashboard;
use App\Filament\User\Widgets\KycStatusWidget;
use App\Filament\User\Widgets\ListingsStatusChart;
use App\Filament\User\Widgets\QuickActionsWidget;
use App\Filament\User\Widgets\RecentListingsWidget;
use App\Filament\User\Widgets\UserStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('dashboard')
            ->login()
            ->brandName('API GharJagga')
            ->brandLogo(null)
            ->darkMode(false)
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
            ])
            ->viteTheme('resources/css/filament/user/theme.css')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.custom-theme'),
            )
            ->topNavigation(false)
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                UserDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                KycStatusWidget::class,
                UserStatsWidget::class,
                ListingsStatusChart::class,
                QuickActionsWidget::class,
                RecentListingsWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
