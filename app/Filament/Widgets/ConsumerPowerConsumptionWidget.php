<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class ConsumerPowerConsumptionWidget extends ChartWidget
{
    use HasRoleVisibility;

    protected static ?string $heading = 'Consumer Power Consumption (kWh) by Time Slots (15-minute intervals)';

    protected int|string|array $columnSpan = 'full';

    //maxheight
    protected static ?string $maxHeight = '200px';
    
    protected static ?int $sort = 3;

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
        $data = $this->getConsumerPowerConsumptionData();

        // Get consumer info for chart title
        $consumerInfo = '';
        if ($this->record && $this->record->property) {
            $consumerInfo = ' - ' . $this->record->property->account_no . ' (' . $this->record->property->address . ')';
        }

        return [
            'labels' => $data['labels'],
            'datasets' => [
                [
                    'label' => 'Power Consumption (kWh)' . $consumerInfo,
                    'data' => $data['values'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
        ];
    }

    protected function getConsumerPowerConsumptionData(): array
    {
        $labels = [];
        for ($h = 0; $h < 24; $h++) {
            for ($m = 0; $m < 60; $m += 15) {
                $labels[] = sprintf('%02d:%02d', $h, $m);
            }
        }

        $kwh_per_interval = array_fill(0, 96, 0);

        // Get usage data for the specific consumer usage record
        if ($this->record && $this->record instanceof ConsumerUsage) {
            $usage = $this->record;
            
            if (is_array($usage->usage_data)) {
                foreach ($usage->usage_data as $item) {
                    $equipments = [];
                    // Check for nested 'equipment_data' structure
                    if (isset($item['equipment_data']) && is_array($item['equipment_data'])) {
                        $equipments = $item['equipment_data'];
                    }
                    // Check for flat structure
                    elseif (isset($item['equipment']) && isset($item['watt']) && isset($item['time_period'])) {
                        $equipments = [$item];
                    }

                    foreach ($equipments as $equipment) {
                        if (!isset($equipment['watt']) || !isset($equipment['time_period'])) {
                            continue;
                        }

                        $watt = (float) $equipment['watt'];
                        $time_periods = is_array($equipment['time_period']) ? $equipment['time_period'] : [$equipment['time_period']];

                        foreach ($time_periods as $time_period_index) {
                            // The time_period value is a 1-based index for the 15-minute intervals.
                            $index = (int) $time_period_index - 1;

                            if ($index >= 0 && $index < 96) {
                                // Each interval is 15 minutes, which is 0.25 of an hour.
                                // kWh = (Watt * hours) / 1000
                                $kwh = ($watt * 0.25) / 1000;
                                $kwh_per_interval[$index] += $kwh;
                            }
                        }
                    }
                }
            }
        } else {
            // Add some test data if no specific record exists
            for ($i = 0; $i < 96; $i++) {
                // Add some random data for demonstration
                if ($i >= 32 && $i <= 40) { // Morning peak (8:00-10:00)
                    $kwh_per_interval[$i] = (rand(200, 400) * 0.25) / 1000;
                } elseif ($i >= 72 && $i <= 80) { // Evening peak (18:00-20:00)
                    $kwh_per_interval[$i] = (rand(150, 350) * 0.25) / 1000;
                } else {
                    $kwh_per_interval[$i] = (rand(50, 150) * 0.25) / 1000;
                }
            }
        }

        return [
            'labels' => $labels,
            'values' => $kwh_per_interval,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                        'text' => 'Power Consumption (kWh)',
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
