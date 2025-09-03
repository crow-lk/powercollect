<?php

namespace App\Filament\Resources\ConsumersResource\Pages;

use App\Filament\Resources\ConsumersResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConsumer extends CreateRecord
{
    protected static string $resource = ConsumersResource::class;

    // after create route to the index page
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    } 
}
