<?php

namespace App\Filament\Resources\CustomerUsageResource\Pages;

use App\Filament\Resources\CustomerUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerUsages extends ListRecords
{
    protected static string $resource = CustomerUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
