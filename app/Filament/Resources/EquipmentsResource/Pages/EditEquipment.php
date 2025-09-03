<?php

namespace App\Filament\Resources\EquipmentsResource\Pages;

use App\Filament\Resources\EquipmentsResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    } 
}
