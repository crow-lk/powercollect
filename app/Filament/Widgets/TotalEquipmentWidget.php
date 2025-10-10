<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalEquipmentWidget extends BaseWidget
{
    use HasRoleVisibility;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];
    
    protected static ?int $sort = 6;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Equipment', Equipment::count())
                ->description('Available equipment units')
                ->descriptionIcon('heroicon-m-cog-6-tooth', IconPosition::Before)
                ->chart([5,2,8,3])
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return static::canViewWidgetByRole();
    }
}
