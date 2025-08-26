<?php

namespace App\Filament\Resources\ConsumerUsageResource\Pages;

use App\Filament\Resources\ConsumerUsageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConsumerUsage extends CreateRecord
{
    protected static string $resource = ConsumerUsageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Transform the nested structure to a flat structure for storage
        $flatUsageData = [];

        if (isset($data['usage_data']) && is_array($data['usage_data'])) {
            foreach ($data['usage_data'] as $propertyPartData) {
                if (isset($propertyPartData['property_part']) && isset($propertyPartData['equipment_data'])) {
                    foreach ($propertyPartData['equipment_data'] as $equipmentData) {
                        $flatUsageData[] = [
                            'property_part' => $propertyPartData['property_part'],
                            'equipment' => $equipmentData['equipment'],
                            'watt' => $equipmentData['watt'],
                            'time_period' => $equipmentData['time_period'],
                        ];
                    }
                }
            }
        }

        $data['usage_data'] = $flatUsageData;

        return $data;
    }
}
