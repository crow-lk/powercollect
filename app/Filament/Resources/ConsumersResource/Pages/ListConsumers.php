<?php

namespace App\Filament\Resources\ConsumersResource\Pages;

use App\Filament\Resources\ConsumersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsumers extends ListRecords
{
    protected static string $resource = ConsumersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
