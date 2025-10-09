<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Solutionforest\FilamentScaffold\FilamentScaffoldPlugin;
use App\Filament\Resources\ConsumersResource;
use App\Filament\Resources\ConsumerUsageResource;



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandLogo(asset('assets/leco.png'))
            ->brandLogoHeight('4rem')
            ->resources([
                ConsumersResource::class,
                ConsumerUsageResource::class,
                \App\Filament\Resources\EquipmentsResource::class,
                \App\Filament\Resources\PropertyResource::class,
                \App\Filament\Resources\PropertyPartResource::class,
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\RoleResource::class,
                \App\Filament\Resources\PermissionResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->widgets([
                // Consumer Search widget (full width at top)
                \App\Filament\Widgets\ConsumerSearchWidget::class,
                
                // Chart widgets (full width at top)
                \App\Filament\Widgets\EquipmentUsageDistributionWidget::class,
                \App\Filament\Widgets\EquipmentUsageFrequency::class,
                \App\Filament\Widgets\PropertyPartWattageWidget::class,
                \App\Filament\Widgets\PowerConsumption::class,
                
                // Stats widgets (will be arranged in 3-column grid via widget properties)
                \App\Filament\Widgets\TotalUsageRecordsWidget::class,
                \App\Filament\Widgets\TotalPropertiesWidget::class,
                \App\Filament\Widgets\TotalEquipmentWidget::class,
                \App\Filament\Widgets\TotalConsumersWidget::class,
                
                // Recent Activity widget (full width at bottom)
                \App\Filament\Widgets\RecentActivityWidget::class,
                
                // Consumer-specific widgets (hidden from dashboard, used only in ConsumerUsage views)
                \App\Filament\Widgets\ConsumerEquipmentUsageFrequencyWidget::class,
                \App\Filament\Widgets\ConsumerWattageDistributionWidget::class,
                \App\Filament\Widgets\ConsumerPowerConsumptionWidget::class,
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
            ]);
    }
}
