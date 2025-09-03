<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumerUsage;
use App\Models\Equipment;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EquipmentUsageFrequency extends ChartWidget
{
    protected static ?string $heading = 'Equipment Usage Frequency';
    
    protected int | string | array $columnSpan = 'full';

    //maxheight
    protected static ?string $maxHeight = '200px';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get all unique equipment names from usage data
        $equipmentNames = ConsumerUsage::all()
            ->flatMap(function ($usage) {
                $equipments = [];
                if (is_array($usage->usage_data)) {
                    foreach ($usage->usage_data as $item) {
                        if (isset($item['equipment_data'])) {
                            // New nested structure
                            foreach ($item['equipment_data'] as $equipment) {
                                $name = $equipment['equipment'] ?? 'Unknown Equipment';
                                $equipments[] = trim(explode('-', $name)[0]);
                            }
                        } else {
                            // Direct structure
                            $name = $item['equipment'] ?? 'Unknown Equipment';
                            $equipments[] = trim(explode('-', $name)[0]);
                        }
                    }
                }
                return $equipments;
            })
            ->unique()
            ->values();

        // Count frequency for each equipment using Laravel Trend for time-based analysis
        $equipmentFrequency = [];
        
        foreach ($equipmentNames as $equipmentName) {
            // Use Trend to count occurrences of this equipment over time
            try {
                $trendData = Trend::query(
                    ConsumerUsage::whereJsonContains('usage_data', ['equipment' => $equipmentName])
                        ->orWhereJsonContains('usage_data', [['equipment' => $equipmentName]]) // For direct structure
                        ->orWhereJsonContains('usage_data', [['equipment_data' => [['equipment' => $equipmentName]]]]) // For nested structure
                )
                ->between(
                    start: now()->subMonths(12),
                    end: now(),
                )
                ->perMonth()
                ->count();
                
                // Sum up all the trend values to get total frequency
                $totalCount = collect($trendData)->sum('aggregate');
                
            } catch (\Exception $e) {
                // Fallback to manual counting if trend fails
                $totalCount = ConsumerUsage::all()->sum(function ($usage) use ($equipmentName) {
                    $count = 0;
                    if (is_array($usage->usage_data)) {
                        foreach ($usage->usage_data as $item) {
                            if (isset($item['equipment_data'])) {
                                // New nested structure
                                foreach ($item['equipment_data'] as $equipment) {
                                    $name = $equipment['equipment'] ?? '';
                                    if (str_starts_with($name, $equipmentName)) {
                                        $count++;
                                    }
                                }
                            } else {
                                // Direct structure
                                $name = $item['equipment'] ?? '';
                                if (str_starts_with($name, $equipmentName)) {
                                    $count++;
                                }
                            }
                        }
                    }
                    return $count;
                });
            }
            
            $equipmentFrequency[$equipmentName] = $totalCount;
        }

        // Remove equipment with zero frequency
        $equipmentFrequency = array_filter($equipmentFrequency, function($count) {
            return $count > 0;
        });

        // Sort by frequency (descending)
        arsort($equipmentFrequency);

        // Limit to top 10 and group the rest into "Others"
        $limit = 10;
        if (count($equipmentFrequency) > $limit) {
            $topEquipments = array_slice($equipmentFrequency, 0, $limit, true);
            $otherEquipments = array_slice($equipmentFrequency, $limit, null, true);
            $otherFrequency = array_sum($otherEquipments);
            
            $equipmentFrequency = $topEquipments;
            if ($otherFrequency > 0) {
                $equipmentFrequency['Others'] = $otherFrequency;
            }
        }
        
        // Prepare data for chart
        $labels = array_keys($equipmentFrequency);
        $data = array_values($equipmentFrequency);
        
        return [
            'datasets' => [
                [
                    'label' => 'Usage Frequency',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 99, 255, 0.8)',
                        'rgba(99, 255, 132, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(255, 99, 255, 1)',
                        'rgba(99, 255, 132, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
