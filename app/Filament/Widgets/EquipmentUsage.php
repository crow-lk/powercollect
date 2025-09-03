<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EquipmentUsage extends ChartWidget
{
    protected static ?string $heading = 'Total Power Usage per 15-minute Interval';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Initialize 96 time slots (for 24 hours * 4 intervals per hour) with 0 usage
        $timeSlots = [];
        for ($h = 0; $h < 24; $h++) {
            for ($m = 0; $m < 60; $m += 15) {
                $start = sprintf('%02d:%02d', $h, $m);
                $timeSlots[$start] = 0;
            }
        }

        // Process consumer usage data
        $usages = ConsumerUsage::all();

        if ($usages->isNotEmpty()) {
            foreach ($usages as $usage) {
                $details = $usage->getEquipmentDetailsAttribute();
                foreach ($details as $detail) {
                    $watt = (float) ($detail['watt'] ?? 0);
                    if ($watt > 0 && isset($detail['time_periods'])) {
                        foreach ($detail['time_periods'] as $timePeriod) {
                            // Time period is like "00:00-00:15". We need the start time.
                            $timeParts = explode('-', $timePeriod);
                            $startTime = $timeParts[0];

                            if (isset($timeSlots[$startTime])) {
                                $timeSlots[$startTime] += $watt;
                            }
                        }
                    }
                }
            }
        }

        $data = collect($timeSlots)->map(function ($watt, $startTime) {
            $end = Carbon::createFromTimeString($startTime)->addMinutes(15)->format('H:i');
            if ($end === '00:00') {
                $end = '24:00';
            }
            return (object) [
                'date' => $startTime . '-' . $end,
                'aggregate' => $watt,
            ];
        })->values();

        return [
            'datasets' => [
                [
                    'label' => 'Total Watts',
                    'data' => $data->map(fn ($value) => $value->aggregate),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data->map(fn ($value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
