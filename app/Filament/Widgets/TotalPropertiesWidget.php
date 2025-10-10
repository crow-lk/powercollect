<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPropertiesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];
    
    protected static ?int $sort = 5;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Properties', Property::count())
                ->description('Registered properties')
                ->descriptionIcon('heroicon-m-building-office-2', IconPosition::Before)
                ->chart([10,18,3,41])
                ->color('success')
        ];
    }
}
