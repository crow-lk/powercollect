<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Models\Consumer;
use App\Models\Equipment;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\View\View;

class EquipmentUsageDistributionWidget extends ChartWidget
{
    use HasRoleVisibility;
    protected static ?string $heading = 'Total wattage ratings by Time Slots (15-minute intervals)';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 2,
    ];



    //maxheight
    protected static ?string $maxHeight = '200px';

    protected static ?string $pollingInterval = null;
    
    protected static ?int $sort = 2;

    public ?int $selectedConsumerId = null;

    protected $listeners = ['consumer-selected' => 'updateConsumerFilter'];

    public function updateConsumerFilter($consumerId = null)
    {
        $this->selectedConsumerId = $consumerId;
        $this->updateChartData();
    }

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

        // Only show data if a consumer is selected
        if (!$this->selectedConsumerId) {
            // Return empty data with message
            return [
                'datasets' => [
                    [
                        'label' => 'Please select a consumer to view data',
                        'data' => [],
                        'backgroundColor' => 'rgba(226, 229, 7, 0.6)',
                        'borderColor' => 'rgb(172, 11, 67)',
                        'borderWidth' => 0.5,
                        'fill' => true,
                    ],
                ],
                'labels' => $periodLabels,
            ];
        }

        // Get usage data for selected consumer only
        $usages = ConsumerUsage::whereHas('property', function ($q) {
            $q->where('consumer_id', $this->selectedConsumerId);
        })->get();

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
                    'label' => 'Total Wattage (Watts)' . $consumerInfo,
                    'data' => $wattValues,
                    'backgroundColor' => 'rgba(226, 229, 7, 0.6)',
                    'borderColor' => 'rgb(172, 11, 67)',
                    'borderWidth' => 0.5,
                    'fill' => true,
                ],
            ],
            'labels' => $periodLabels,
        ];
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
                        'text' => 'Time (15-minute Intervals)',
                    ],
                    'ticks' => [
                        'maxRotation' => 90,
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

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return static::canViewWidgetByRole();
    }
}
