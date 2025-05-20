<?php

namespace App\Filament\Resources\CustomersResource\Pages;

use App\Filament\Resources\CustomersResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomers extends ManageRecords
{
    protected static string $resource = CustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
