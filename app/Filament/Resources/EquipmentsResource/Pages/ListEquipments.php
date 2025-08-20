<?php

namespace App\Filament\Resources\EquipmentsResource\Pages;

use App\Filament\Resources\EquipmentsResource;
use Filament\Resources\Pages\ListRecords;

class ListEquipments extends ListRecords
{
    protected static string $resource = EquipmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
