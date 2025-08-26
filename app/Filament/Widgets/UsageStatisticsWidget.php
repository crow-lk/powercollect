<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsageStatisticsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalUsages = ConsumerUsage::count();
        $thisMonthUsages = ConsumerUsage::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonthUsages = ConsumerUsage::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $monthlyIncrease = $lastMonthUsages > 0
            ? round((($thisMonthUsages - $lastMonthUsages) / $lastMonthUsages) * 100, 1)
            : 0;

        $totalKva = ConsumerUsage::get()->sum(function ($usage) {
            return $usage->total_kva;
        });

        $averageKvaPerUsage = $totalUsages > 0 ? round($totalKva / $totalUsages, 2) : 0;

        return [
            Stat::make('Total Usage Records', $totalUsages)
                ->description('All recorded usages')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('This Month', $thisMonthUsages)
                ->description($monthlyIncrease >= 0 ? "+{$monthlyIncrease}% from last month" : "{$monthlyIncrease}% from last month")
                ->descriptionIcon($monthlyIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyIncrease >= 0 ? 'success' : 'danger'),

            Stat::make('Total KVA', number_format($totalKva, 2))
                ->description("Avg: {$averageKvaPerUsage} KVA per usage")
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning'),
        ];
    }
}
