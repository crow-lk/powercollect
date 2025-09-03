<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ThisMonthUsageWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;
    
    protected static ?int $sort = 8;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $thisMonthUsages = ConsumerUsage::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonthUsages = ConsumerUsage::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $monthlyIncrease = 0;
        if ($lastMonthUsages > 0) {
            $monthlyIncrease = round((($thisMonthUsages - $lastMonthUsages) / $lastMonthUsages) * 100, 1);
        } elseif ($thisMonthUsages > 0) {
            $monthlyIncrease = 100;
        }

        return [
            Stat::make('This Month', $thisMonthUsages)
                ->description($monthlyIncrease >= 0 ? "+{$monthlyIncrease}% from last month" : "{$monthlyIncrease}% from last month")
                ->descriptionIcon($monthlyIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyIncrease >= 0 ? 'success' : 'danger'),
        ];
    }
}
