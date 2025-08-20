<?php

namespace App\Filament\Resources\HousePartResource\Pages;

use App\Filament\Resources\HousePartResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListHouseParts extends ListRecords
{
    protected static string $resource = HousePartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
