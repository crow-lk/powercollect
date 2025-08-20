<?php

namespace App\Filament\Resources\PropertyPartResource\Pages;

use App\Filament\Resources\PropertyPartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyParts extends ListRecords
{
    protected static string $resource = PropertyPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
