<?php

namespace App\Filament\Resources\PowerUsageByTimeSlotesResource\Widgets;

use App\Models\ConsumerUsage;
use App\Models\Equipment;
use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Power Usage Analytics Dashboard';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '600px';

    protected function getData(): array
    {
        // Initialize hourly data (24 hours)
        $hourlyData = [];
        $hourlyLabels = [];
        $equipmentData = [];
        
        // Create labels for 24 hours
        for ($hour = 0; $hour < 24; $hour++) {
            $nextHour = ($hour + 1) % 24;
            $label = sprintf('%02d:00 - %02d:00', $hour, $nextHour);
            $hourlyLabels[] = $label;
            $hourlyData[$hour] = 0; // Initialize with 0 watts
            $equipmentData[$hour] = []; // Initialize equipment data array
        }

        // Get all usage data and aggregate watts by hours
        $usages = ConsumerUsage::with('consumer')->get();

        foreach ($usages as $usage) {
            if (is_array($usage->usage_data)) {
                foreach ($usage->usage_data as $item) {
                    if (isset($item['equipment_data'])) {
                        // New nested structure
                        foreach ($item['equipment_data'] as $equipment) {
                            $watt = floatval($equipment['watt'] ?? 0);
                            $equipmentName = $equipment['name'] ?? 'Unknown Equipment';
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
                                    
                                    // Store equipment-specific data
                                    if (!isset($equipmentData[$hour][$equipmentName])) {
                                        $equipmentData[$hour][$equipmentName] = 0;
                                    }
                                    $equipmentData[$hour][$equipmentName] += $watt;
                                }
                            }
                        }
                    } else {
                        // Direct structure (fallback)
                        $watt = floatval($item['watt'] ?? 0);
                        $equipmentName = $item['name'] ?? 'Unknown Equipment';
                        $timePeriods = $item['time_period'] ?? [];
                        
                        if (!is_array($timePeriods)) {
                            $timePeriods = [$timePeriods];
                        }
                        
                        foreach ($timePeriods as $period) {
                            if ($period >= 1 && $period <= 96) {
                                // Convert 15-minute period to hour
                                $hour = intval(($period - 1) / 4);
                                $hourlyData[$hour] += $watt;
                                
                                // Store equipment-specific data
                                if (!isset($equipmentData[$hour][$equipmentName])) {
                                    $equipmentData[$hour][$equipmentName] = 0;
                                }
                                $equipmentData[$hour][$equipmentName] += $watt;
                            }
                        }
                    }
                }
            }
        }

        // Convert to ordered arrays
        $wattValues = array_values($hourlyData);
        
        // Get top 5 equipment types for separate datasets
        $equipmentTypes = [];
        foreach ($equipmentData as $hourData) {
            foreach ($hourData as $equipmentName => $watt) {
                if (!isset($equipmentTypes[$equipmentName])) {
                    $equipmentTypes[$equipmentName] = 0;
                }
                $equipmentTypes[$equipmentName] += $watt;
            }
        }
        
        // Sort by total usage and get top 5
        arsort($equipmentTypes);
        $topEquipment = array_slice(array_keys($equipmentTypes), 0, 5, true);
        
        // Create datasets for top equipment
        $equipmentDatasets = [];
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(245, 158, 11, 0.8)',   // Yellow
            'rgba(139, 92, 246, 0.8)',   // Purple
        ];
        
        foreach ($topEquipment as $index => $equipmentName) {
            $equipmentHourlyData = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $equipmentHourlyData[] = $equipmentData[$hour][$equipmentName] ?? 0;
            }
            
            $equipmentDatasets[] = [
                'label' => $equipmentName,
                'data' => $equipmentHourlyData,
                'backgroundColor' => $colors[$index],
                'borderColor' => str_replace('0.8', '1', $colors[$index]),
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
            ];
        }

        return [
            'datasets' => array_merge([
                [
                    'label' => 'Total Power Consumption',
                    'data' => $wattValues,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.3)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
            ], $equipmentDatasets),
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
                        'text' => 'Time (24-Hour Period)',
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                    ],
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                        'drawBorder' => false,
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Power Consumption (Watts)',
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                    ],
                    'beginAtZero' => true,
                    'min' => 0,
                    'suggestedMax' => null,
                    'ticks' => [
                        'stepSize' => 200,
                        'callback' => 'function(value) { return value + " W"; }',
                        'font' => [
                            'size' => 11,
                        ],
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
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => 'rgba(255, 255, 255, 1)',
                    'bodyColor' => 'rgba(255, 255, 255, 1)',
                    'borderColor' => 'rgba(255, 255, 255, 0.2)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'layout' => [
                'padding' => [
                    'top' => 30,
                    'bottom' => 30,
                    'left' => 30,
                    'right' => 30,
                ],
            ],
            'elements' => [
                'point' => [
                    'radius' => 4,
                    'hoverRadius' => 6,
                ],
                'line' => [
                    'borderWidth' => 2,
                ],
            ],
        ];
    }
}
