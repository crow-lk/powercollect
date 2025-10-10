<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumerUsage;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Illuminate\Database\Eloquent\Model;

class ConsumerEquipmentUsageFrequencyWidget extends ChartWidget
{
    use HasRoleVisibility;

    protected static ?string $heading = 'Consumer Equipment Usage Frequency';
    
    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '200px';
    
    protected static ?int $sort = 2;

    public ?Model $record = null;

    public static function canView(): bool
    {
        if (! static::canViewWidgetByRole()) {
            return false;
        }

        // Only show this widget when we have a record (i.e., not on the main dashboard)
        return request()->route() && str_contains(request()->route()->getName(), 'consumer-usage');
    }

    protected function getData(): array
    {
        // Initialize equipment frequency array
        $equipmentFrequency = [];

        // Get equipment usage data for the specific consumer usage record
        if ($this->record && $this->record instanceof ConsumerUsage) {
            $usage = $this->record;
            
            if (is_array($usage->usage_data)) {
                foreach ($usage->usage_data as $item) {
                    if (isset($item['equipment_data'])) {
                        // New nested structure
                        foreach ($item['equipment_data'] as $equipment) {
                            $name = $equipment['equipment'] ?? 'Unknown Equipment';
                            // Extract equipment type (first part before dash)
                            $equipmentType = trim(explode('-', $name)[0]);
                            
                            // Count the frequency (number of time periods this equipment is used)
                            $timePeriods = $equipment['time_period'] ?? [];
                            if (!is_array($timePeriods)) {
                                $timePeriods = [$timePeriods];
                            }
                            
                            // Add the count of time periods for this equipment
                            if (!isset($equipmentFrequency[$equipmentType])) {
                                $equipmentFrequency[$equipmentType] = 0;
                            }
                            $equipmentFrequency[$equipmentType] += count($timePeriods);
                        }
                    } else {
                        // Direct structure (fallback)
                        $name = $item['equipment'] ?? 'Unknown Equipment';
                        $equipmentType = trim(explode('-', $name)[0]);
                        
                        $timePeriods = $item['time_period'] ?? [];
                        if (!is_array($timePeriods)) {
                            $timePeriods = [$timePeriods];
                        }
                        
                        if (!isset($equipmentFrequency[$equipmentType])) {
                            $equipmentFrequency[$equipmentType] = 0;
                        }
                        $equipmentFrequency[$equipmentType] += count($timePeriods);
                    }
                }
            }
        } else {
            // Add some test data if no specific record exists
            $equipmentFrequency = [
                'Air Conditioner' => 45,
                'Refrigerator' => 96, // Always on
                'Television' => 20,
                'Washing Machine' => 8,
                'Microwave' => 12,
                'Fan' => 35,
                'Lights' => 40,
            ];
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

        // Get consumer info for chart title
        $consumerInfo = '';
        if ($this->record && $this->record->property) {
            $consumerInfo = ' - ' . $this->record->property->account_no . ' (' . $this->record->property->address . ')';
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Usage Frequency (Time Periods)' . $consumerInfo,
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green
                        'rgba(239, 68, 68, 0.8)',   // Red
                        'rgba(245, 158, 11, 0.8)',  // Amber
                        'rgba(59, 130, 246, 0.8)',  // Blue
                        'rgba(147, 51, 234, 0.8)',  // Purple
                        'rgba(236, 72, 153, 0.8)',  // Pink
                        'rgba(156, 163, 175, 0.8)', // Gray
                        'rgba(20, 184, 166, 0.8)',  // Teal
                        'rgba(251, 146, 60, 0.8)',  // Orange
                        'rgba(132, 204, 22, 0.8)',  // Lime
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(147, 51, 234, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(156, 163, 175, 1)',
                        'rgba(20, 184, 166, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(132, 204, 22, 1)',
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

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'responsive' => true,
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Equipment Types',
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Usage Frequency (Time Periods)',
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
