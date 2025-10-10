<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalUsageRecordsWidget extends BaseWidget
{
    use HasRoleVisibility;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];
    
    protected static ?int $sort = 4;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Usage Records', ConsumerUsage::count())
                ->description('All recorded usages')
                ->descriptionIcon('heroicon-m-chart-bar', IconPosition::Before)
                ->chart([10,3,15,7])
                ->color('primary')
        ];
    }

    public static function canView(): bool
    {
        return static::canViewWidgetByRole();
    }

}
