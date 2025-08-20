<?php

namespace App\Filament\User\Resources\CustomerUsageResource\Pages;

use App\Filament\User\Resources\CustomerUsageResource;
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
