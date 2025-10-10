<?php

namespace App\Filament\Resources\ConsumerUsageResource\Pages;

use App\Filament\Resources\ConsumerUsageResource;
use App\Filament\Widgets\ConsumerWattageDistributionWidget;
use App\Filament\Widgets\ConsumerEquipmentUsageFrequencyWidget;
use App\Filament\Widgets\ConsumerPowerConsumptionWidget;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Widgets\ConsumerPropertyPartWattageWidget;

class ViewConsumerUsage extends ViewRecord
{
    protected static string $resource = ConsumerUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to List')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ConsumerPowerConsumptionWidget::class,
            ConsumerWattageDistributionWidget::class,
            ConsumerEquipmentUsageFrequencyWidget::class,
            ConsumerPropertyPartWattageWidget::class,
        ];
    }
}
