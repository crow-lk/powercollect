<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumerUsage;
use App\Models\Consumer;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PropertyPartWattageWidget extends ChartWidget
{
    protected static ?string $heading = 'Property Part - Wattage Usage';
    
    protected int | string | array $columnSpan = 'full';

    // Max height
    protected static ?string $maxHeight = '200px';
    
    protected static ?int $sort = 4;

    public ?int $selectedConsumerId = null;

    protected $listeners = ['consumer-selected' => 'updateConsumerFilter'];

    public function updateConsumerFilter($consumerId = null)
    {
        $this->selectedConsumerId = $consumerId;
        $this->updateChartData();
    }

    protected function getData(): array
    {
        // Only show data if a consumer is selected
        if (!$this->selectedConsumerId) {
            return [
                'datasets' => [
                    [
                        'label' => 'Please select a consumer to view property part wattage',
                        'data' => [],
                        'backgroundColor' => [],
                        'borderColor' => [],
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => [],
            ];
        }

        // Get usage data for selected consumer only
        $propertyPartWattage = [];
        
        $usageRecords = ConsumerUsage::whereHas('property', function ($q) {
            $q->where('consumer_id', $this->selectedConsumerId);
        })->get();

        foreach ($usageRecords as $usage) {
            if (is_array($usage->usage_data)) {
                foreach ($usage->usage_data as $item) {
                    if (isset($item['equipment_data'])) {
                        // New nested structure
                        $propertyPart = $item['property_part'] ?? 'Unknown';
                        
                        foreach ($item['equipment_data'] as $equipment) {
                            $watt = floatval($equipment['watt'] ?? 0);
                            if (!isset($propertyPartWattage[$propertyPart])) {
                                $propertyPartWattage[$propertyPart] = 0;
                            }
                            $propertyPartWattage[$propertyPart] += $watt;
                        }
                    } else {
                        // Direct structure
                        $propertyPart = $item['property_part'] ?? 'Unknown';
                        $watt = floatval($item['watt'] ?? 0);
                        
                        if (!isset($propertyPartWattage[$propertyPart])) {
                            $propertyPartWattage[$propertyPart] = 0;
                        }
                        $propertyPartWattage[$propertyPart] += $watt;
                    }
                }
            }
        }

        // Remove property parts with zero wattage
        $propertyPartWattage = array_filter($propertyPartWattage, function($watt) {
            return $watt > 0;
        });

        // Sort by wattage (descending)
        arsort($propertyPartWattage);

        // Limit to top 15 and group the rest into "Others"
        $limit = 15;
        if (count($propertyPartWattage) > $limit) {
            $topPropertyParts = array_slice($propertyPartWattage, 0, $limit, true);
            $otherPropertyParts = array_slice($propertyPartWattage, $limit, null, true);
            $otherWattage = array_sum($otherPropertyParts);
            
            $propertyPartWattage = $topPropertyParts;
            if ($otherWattage > 0) {
                $propertyPartWattage['Others'] = $otherWattage;
            }
        }
        
        // Prepare data for chart
        $labels = array_keys($propertyPartWattage);
        $data = array_values($propertyPartWattage);
        
        // Get consumer info for chart title
        $consumerInfo = '';
        if ($this->selectedConsumerId) {
            $consumer = Consumer::find($this->selectedConsumerId);
            if ($consumer) {
                $consumerInfo = ' - ' . $consumer->name;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Total Wattage (W)' . $consumerInfo,
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green
                        'rgba(59, 130, 246, 0.8)',  // Blue
                        'rgba(245, 101, 101, 0.8)', // Red
                        'rgba(251, 191, 36, 0.8)',  // Yellow
                        'rgba(168, 85, 247, 0.8)',  // Purple
                        'rgba(236, 72, 153, 0.8)',  // Pink
                        'rgba(20, 184, 166, 0.8)',  // Teal
                        'rgba(251, 146, 60, 0.8)',  // Orange
                        'rgba(156, 163, 175, 0.8)', // Gray
                        'rgba(99, 102, 241, 0.8)',  // Indigo
                        'rgba(239, 68, 68, 0.8)',   // Red variant
                        'rgba(16, 185, 129, 0.8)',  // Emerald
                        'rgba(139, 92, 246, 0.8)',  // Violet
                        'rgba(244, 63, 94, 0.8)',   // Rose
                        'rgba(6, 182, 212, 0.8)',   // Cyan
                        'rgba(107, 114, 128, 0.8)', // Others
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(245, 101, 101, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(20, 184, 166, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(156, 163, 175, 1)',
                        'rgba(99, 102, 241, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(244, 63, 94, 1)',
                        'rgba(6, 182, 212, 1)',
                        'rgba(107, 114, 128, 1)',
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Total Wattage (W)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Property Parts',
                    ],
                ],
            ],
        ];
    }
}
