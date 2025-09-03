<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPropertiesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Properties', Property::count())
                ->description('Registered properties')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),
        ];
    }
}
