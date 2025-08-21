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
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
