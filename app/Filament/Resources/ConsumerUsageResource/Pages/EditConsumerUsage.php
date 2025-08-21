<?php

namespace App\Filament\Resources\ConsumerUsageResource\Pages;

use App\Filament\Resources\ConsumerUsageResource;
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

    protected function mutateFormDataBeforeSave(array $data): array
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
                            'kva' => $equipmentData['kva'],
                            'time_period' => $equipmentData['time_period'],
                        ];
                    }
                }
            }
        }

        $data['usage_data'] = $flatUsageData;

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Transform flat structure to nested structure for editing
        $nestedUsageData = [];

        if (isset($data['usage_data']) && is_array($data['usage_data'])) {
            // Group by property_part
            $groupedData = [];
            foreach ($data['usage_data'] as $item) {
                $propertyPart = $item['property_part'] ?? 'Unknown';
                if (! isset($groupedData[$propertyPart])) {
                    $groupedData[$propertyPart] = [];
                }
                $groupedData[$propertyPart][] = [
                    'equipment' => $item['equipment'],
                    'kva' => $item['kva'],
                    'time_period' => $item['time_period'],
                ];
            }

            // Convert to nested structure
            foreach ($groupedData as $propertyPart => $equipmentList) {
                $nestedUsageData[] = [
                    'property_part' => $propertyPart,
                    'equipment_data' => $equipmentList,
                ];
            }
        }

        $data['usage_data'] = $nestedUsageData;

        return $data;
    }
}
