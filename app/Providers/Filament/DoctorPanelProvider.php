<?php

namespace App\Providers\Filament;

use App\AvatarProviders\CustomUiAvatarsProvider;
use App\Filament\Doctor\Pages\Dashboard;
use App\Filament\Doctor\Pages\ZoomSettings;
use App\Filament\Doctor\Resources\AppointmentResource;
use App\Filament\Doctor\Resources\BookingResource;
use App\Http\Middleware\EnsureDoctorRole;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DoctorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('doctor')
            ->path('doctor')
            ->login()
            ->colors([
                'primary' => '#0ea5e9',
            ])
            ->brandName('Doctor Dashboard')
            ->pages([
                Dashboard::class,
                ZoomSettings::class,
            ])
            ->resources([
                AppointmentResource::class,
                BookingResource::class,
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
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureDoctorRole::class,
            ])
            ->authGuard('web')
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => view('filament.language-switch')->render(),
            )
            ->defaultAvatarProvider(CustomUiAvatarsProvider::class);
    }
}
