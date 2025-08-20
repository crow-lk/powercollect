<?php

namespace App\Filament\Resources\CustomerUsageResource\Pages;

use App\Filament\Resources\CustomerUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerUsage extends EditRecord
{
    protected static string $resource = CustomerUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
