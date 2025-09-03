<?php

namespace App\Filament\Resources\PropertyPartResource\Pages;

use App\Filament\Resources\PropertyPartResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyPart extends CreateRecord
{
    protected static string $resource = PropertyPartResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
