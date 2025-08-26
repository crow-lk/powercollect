<?php

namespace App\Filament\Widgets;

use App\Models\Consumer;
use App\Models\Equipment;
use App\Models\Property;
use App\Models\PropertyPart;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Properties data
        $totalProperties = Property::count();
        $totalPropertyParts = PropertyPart::count();

        // Equipment data
        $totalEquipment = Equipment::count();

        // Consumer data
        $totalConsumers = Consumer::count();

        // User data
        $totalUsers = User::count();
        $activeUsers = User::where('email_verified_at', '!=', null)->count();
        $activePercentage = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;

        return [
            Stat::make('Total Properties', $totalProperties)
                ->description('Registered properties')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make('Property Parts', $totalPropertyParts)
                ->description('Available property parts')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),

            Stat::make('Total Equipment', $totalEquipment)
                ->description('Available equipment units')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('warning'),

            Stat::make('Total Consumers', $totalConsumers)
                ->description('Registered consumers')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Active Users', $activeUsers)
                ->description("{$activePercentage}% of {$totalUsers} total users")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
