<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class ConsumerWattageDistributionWidget extends ChartWidget
{
    use HasRoleVisibility;

    protected static ?string $heading = 'Consumer Wattage Usage by Time Slots (15-minute intervals)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '200px';

    protected static ?string $pollingInterval = null;
    
    protected static ?int $sort = 1;

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

        // Get usage data for the specific consumer usage record
        if ($this->record && $this->record instanceof ConsumerUsage) {
            $usage = $this->record;
            
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
        } else {
            // Add some test data if no specific record or no data exists
            for ($i = 1; $i <= 96; $i++) {
                // Add some random data for demonstration
                if ($i >= 32 && $i <= 40) { // Morning peak
                    $periodData[$i] = rand(200, 400);
                } elseif ($i >= 72 && $i <= 80) { // Evening peak
                    $periodData[$i] = rand(150, 350);
                } else {
                    $periodData[$i] = rand(50, 150);
                }
            }
        }

        // Convert to ordered arrays
        $wattValues = array_values($periodData);

        // Get consumer info for chart title
        $consumerInfo = '';
        if ($this->record && $this->record->property) {
            $consumerInfo = ' - ' . $this->record->property->account_no . ' (' . $this->record->property->address . ')';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Consumer Wattage Usage (Watts)' . $consumerInfo,
                    'data' => $wattValues,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => 'rgb(37, 99, 235)',
                    'borderWidth' => 0.5,
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
}
