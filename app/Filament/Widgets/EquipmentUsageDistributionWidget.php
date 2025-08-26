<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Models\Equipment;
use Filament\Widgets\ChartWidget;

class EquipmentUsageDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Equipment Usage Distribution';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'half';

    protected function getData(): array
    {
        // Get equipment usage distribution
        $equipmentUsage = [];
        $equipmentNames = [];
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(245, 158, 11, 0.8)',   // Yellow
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(139, 92, 246, 0.8)',   // Purple
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(34, 197, 94, 0.8)',    // Emerald
            'rgba(251, 146, 60, 0.8)',   // Orange
        ];

        // Get all usage data and count equipment usage
        $usages = ConsumerUsage::all();
        $equipmentCounts = [];

        foreach ($usages as $usage) {
            if (is_array($usage->usage_data)) {
                foreach ($usage->usage_data as $item) {
                    if (isset($item['equipment_data'])) {
                        // New nested structure
                        foreach ($item['equipment_data'] as $equipment) {
                            $equipmentName = $equipment['equipment'] ?? 'Unknown';
                            $equipmentCounts[$equipmentName] = ($equipmentCounts[$equipmentName] ?? 0) + 1;
                        }
                    } else {
                        // Direct structure
                        $equipmentName = $item['equipment'] ?? 'Unknown';
                        $equipmentCounts[$equipmentName] = ($equipmentCounts[$equipmentName] ?? 0) + 1;
                    }
                }
            }
        }

        // Sort by usage count and take top 8
        arsort($equipmentCounts);
        $topEquipment = array_slice($equipmentCounts, 0, 8, true);

        $equipmentNames = array_keys($topEquipment);
        $equipmentUsage = array_values($topEquipment);

        return [
            'datasets' => [
                [
                    'data' => $equipmentUsage,
                    'backgroundColor' => array_slice($colors, 0, count($equipmentUsage)),
                    'borderColor' => array_slice($colors, 0, count($equipmentUsage)),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $equipmentNames,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
