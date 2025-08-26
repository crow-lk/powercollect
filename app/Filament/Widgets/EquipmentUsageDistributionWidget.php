<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Models\Equipment;
use Filament\Widgets\ChartWidget;

class EquipmentUsageDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Power Usage by Time Slots';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Initialize hourly data (24 hours)
        $hourlyData = [];
        $hourlyLabels = [];
        
        // Create labels for 24 hours
        for ($hour = 0; $hour < 24; $hour++) {
            $nextHour = ($hour + 1) % 24;
            $label = sprintf('%02d:00 - %02d:00', $hour, $nextHour);
            $hourlyLabels[] = $label;
            $hourlyData[$hour] = 0; // Initialize with 0 watts
        }

        // Get all usage data and aggregate watts by hours
        $usages = ConsumerUsage::all();

        foreach ($usages as $usage) {
            if (is_array($usage->usage_data)) {
                foreach ($usage->usage_data as $item) {
                    if (isset($item['equipment_data'])) {
                        // New nested structure
                        foreach ($item['equipment_data'] as $equipment) {
                            $watt = floatval($equipment['watt'] ?? 0);
                            $timePeriods = $equipment['time_period'] ?? [];
                            
                            // Handle both array and single value time periods
                            if (!is_array($timePeriods)) {
                                $timePeriods = [$timePeriods];
                            }
                            
                            foreach ($timePeriods as $period) {
                                if ($period >= 1 && $period <= 96) {
                                    // Convert 15-minute period to hour (1-4 = hour 0, 5-8 = hour 1, etc.)
                                    $hour = intval(($period - 1) / 4);
                                    $hourlyData[$hour] += $watt;
                                }
                            }
                        }
                    } else {
                        // Direct structure (fallback)
                        $watt = floatval($item['watt'] ?? 0);
                        $timePeriods = $item['time_period'] ?? [];
                        
                        if (!is_array($timePeriods)) {
                            $timePeriods = [$timePeriods];
                        }
                        
                        foreach ($timePeriods as $period) {
                            if ($period >= 1 && $period <= 96) {
                                // Convert 15-minute period to hour
                                $hour = intval(($period - 1) / 4);
                                $hourlyData[$hour] += $watt;
                            }
                        }
                    }
                }
            }
        }

        // Convert to ordered arrays
        $wattValues = array_values($hourlyData);

        return [
            'datasets' => [
                [
                    'label' => 'Power Consumption (Watts)',
                    'data' => $wattValues,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $hourlyLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Time (Hourly Intervals)',
                    ],
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Power Consumption (Watts)',
                    ],
                    'beginAtZero' => true,
                    'min' => 0,
                    'suggestedMax' => null,
                    'ticks' => [
                        'stepSize' => 100,
                        'callback' => 'function(value) { return value + " W"; }',
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                        'drawBorder' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'layout' => [
                'padding' => [
                    'top' => 20,
                    'bottom' => 20,
                    'left' => 20,
                    'right' => 20,
                ],
            ],
        ];
    }
}
