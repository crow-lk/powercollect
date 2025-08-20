<?php

namespace App\Filament\User\Resources\ConsumerUsageResource\Pages;

use App\Filament\User\Resources\ConsumerUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsumerUsage extends EditRecord
{
    protected static string $resource = ConsumerUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
