<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use App\Livewire\CustomProfileComponent;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Filament\Navigation\MenuItem;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->darkMode(false)
            ->brandName('Filament Demo')
            ->font('Poppins')
            ->login()
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
            )
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
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
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                ->slug('my-profile')
                ->setTitle('My Profile')
                ->setNavigationLabel('My Profile')
                ->setNavigationGroup('Group Profile')
                ->setIcon('heroicon-o-user')
                ->setSort(10)
                ->canAccess(fn () => auth()->user()->id === 1)
                ->shouldRegisterNavigation(false)
                ->shouldShowDeleteAccountForm(false)
                ->shouldShowSanctumTokens()
                ->shouldShowBrowserSessionsForm()
                ->shouldShowAvatarForm()
                ->customProfileComponents([
                    \App\Livewire\CustomProfileComponent::class,
                ])
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                ->label(fn() => auth()->user()->name)
                ->url(fn (): string => EditProfilePage::getUrl())
                ->icon('heroicon-m-user-circle')
            ]);
    }
}
