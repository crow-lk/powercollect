<?php

namespace App\Filament\Widgets;

use App\Models\PropertyPart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyPartsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Property Parts', PropertyPart::count())
                ->description('Available property parts')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),
        ];
    }
}
