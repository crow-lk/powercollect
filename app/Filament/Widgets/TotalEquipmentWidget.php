<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalEquipmentWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Equipment', Equipment::count())
                ->description('Available equipment')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('primary'),
        ];
    }
}