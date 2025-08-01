<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalCustomersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}

namespace App\Filament\Resources\CustomersResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalCustomersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //
        ];
    }
}
