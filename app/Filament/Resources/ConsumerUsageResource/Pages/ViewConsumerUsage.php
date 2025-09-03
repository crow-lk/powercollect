<?php

namespace App\Filament\Resources\ConsumerUsageResource\Pages;

use App\Filament\Resources\ConsumerUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

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
}
