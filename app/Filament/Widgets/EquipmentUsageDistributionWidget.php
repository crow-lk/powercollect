<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Models\Equipment;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\View\View;

class EquipmentUsageDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Power Usage by Time Slots (15-minute intervals)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Initialize 15-minute interval data (96 periods)
        $periodData = [];
        $periodLabels = [];
        
        // Create labels for 96 fifteen-minute periods
        for ($period = 1; $period <= 96; $period++) {
            // Calculate hours and minutes for start and end of period
            $startMinutes = ($period - 1) * 15;
            $endMinutes = $period * 15;
            
            $startHour = intval($startMinutes / 60);
            $startMin = $startMinutes % 60;
            
            $endHour = intval($endMinutes / 60);
            $endMin = $endMinutes % 60;
            
            // Handle midnight wrap-around for the last period
            if ($period == 96) {
                $label = sprintf('%02d:%02d - 00:00', $startHour, $startMin);
            } else {
                $label = sprintf('%02d:%02d - %02d:%02d', $startHour, $startMin, $endHour, $endMin);
            }
            
            $periodLabels[] = $label;
            $periodData[$period] = 0; // Initialize with 0 watts
        }

        // Get all usage data and aggregate watts by 15-minute periods
        $usages = ConsumerUsage::all();

        // Add some test data if no data exists
        if ($usages->isEmpty()) {
            // Generate some sample data for demonstration
            for ($i = 1; $i <= 96; $i++) {
                // Add some random data for testing
                if ($i >= 32 && $i <= 40) { // Morning peak
                    $periodData[$i] = rand(800, 1200);
                } elseif ($i >= 72 && $i <= 80) { // Evening peak
                    $periodData[$i] = rand(600, 1000);
                } else {
                    $periodData[$i] = rand(200, 500);
                }
            }
        }

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
                                    $periodData[$period] += $watt;
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
                                $periodData[$period] += $watt;
                            }
                        }
                    }
                }
            }
        }

        // Convert to ordered arrays
        $wattValues = array_values($periodData);

        return [
            'datasets' => [
                [
                    'label' => 'Power Consumption (Watts)',
                    'data' => $wattValues,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1,
                    'fill' => true,
                ],
            ],
            'labels' => $periodLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Time (15-minute Intervals)',
                    ],
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 0,
                        'maxTicksLimit' => 24,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Power Consumption (Watts)',
                    ],
                    'beginAtZero' => true,
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
        ];
    }
}