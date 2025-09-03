<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalUsageRecordsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Usage Records', ConsumerUsage::count())
                ->description('All recorded usages')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}
