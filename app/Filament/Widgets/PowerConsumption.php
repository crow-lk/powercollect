<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Consumer;

class PowerConsumption extends ChartWidget
{
    protected static ?string $heading = 'Power Consumption(kWh) by Time Slots (15-minute intervals)';

    protected int|string|array $columnSpan = 'full';

    //maxheight
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
        $data = $this->getPowerConsumptionData();

        // Get consumer info for chart title
        $consumerInfo = '';
        $label = 'Power Consumption (kWh)';
        
        if ($this->selectedConsumerId) {
            $consumer = Consumer::find($this->selectedConsumerId);
            if ($consumer) {
                $consumerInfo = ' - ' . $consumer->name;
                $label = 'Power Consumption (kWh)' . $consumerInfo;
            }
        } else {
            $label = 'Please select a consumer to view power consumption';
        }

        return [
            'labels' => $data['labels'],
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $data['values'],
                ],
            ],
        ];
    }

    protected function getPowerConsumptionData(): array
    {
        $labels = [];
        for ($h = 0; $h < 24; $h++) {
            for ($m = 0; $m < 60; $m += 15) {
                $labels[] = sprintf('%02d:%02d', $h, $m);
            }
        }

        $kwh_per_interval = array_fill(0, 96, 0);

        // Only get data if consumer is selected
        if (!$this->selectedConsumerId) {
            return [
                'labels' => $labels,
                'values' => $kwh_per_interval,
            ];
        }

        // Get usage data for selected consumer only
        $all_usages = \App\Models\ConsumerUsage::whereHas('property', function ($q) {
            $q->where('consumer_id', $this->selectedConsumerId);
        })->get();

        foreach ($all_usages as $usage) {
            // The 'usage_data' attribute is cast to an array by the model.
            $usage_data = $usage->usage_data;

            if (!is_array($usage_data)) {
                continue;
            }

            foreach ($usage_data as $item) {
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

        return [
            'labels' => $labels,
            'values' => $kwh_per_interval,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
