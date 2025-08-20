<?php

namespace App\Filament\Resources\ConsumerUsageResource\Pages;

use App\Filament\Resources\ConsumerUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsumerUsages extends ListRecords
{
    protected static string $resource = ConsumerUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
