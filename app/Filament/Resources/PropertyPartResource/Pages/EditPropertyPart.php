<?php

namespace App\Filament\Resources\PropertyPartResource\Pages;

use App\Filament\Resources\PropertyPartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyPart extends EditRecord
{
    protected static string $resource = PropertyPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
