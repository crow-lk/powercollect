<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class KvaUsageChartWidget extends ChartWidget
{
    protected static ?string $heading = 'KVA Usage Trends';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get data for the last 12 months
        $months = [];
        $kvaData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            // Calculate total KVA for this month
            $monthlyKva = ConsumerUsage::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->get()
                ->sum(function ($usage) {
                    return $usage->total_kva;
                });

            $kvaData[] = round($monthlyKva, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total KVA Usage',
                    'data' => $kvaData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
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
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'KVA',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
