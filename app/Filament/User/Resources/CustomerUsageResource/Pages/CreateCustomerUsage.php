<?php

namespace App\Filament\User\Resources\CustomerUsageResource\Pages;

use App\Filament\User\Resources\CustomerUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerUsage extends CreateRecord
{
    protected static string $resource = CustomerUsageResource::class;
}
