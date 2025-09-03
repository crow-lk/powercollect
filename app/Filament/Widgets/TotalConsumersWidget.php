<?php

namespace App\Filament\Widgets;

use App\Models\Consumer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalConsumersWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;
    
    protected static ?int $sort = 7;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Consumers', Consumer::count())
                ->description('Registered consumers')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
