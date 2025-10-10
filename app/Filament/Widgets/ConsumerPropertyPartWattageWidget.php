<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ConsumerUsage;
use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Illuminate\Database\Eloquent\Model;

class ConsumerPropertyPartWattageWidget extends ChartWidget
{
    use HasRoleVisibility;

    protected static ?string $heading = 'Consumer Property Part - Wattage Usage';
    
    protected int | string | array $columnSpan = 'full';

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
        // Initialize property part wattage array
        $propertyPartWattage = [];

        // Get property part wattage data for the specific consumer usage record
        if ($this->record && $this->record instanceof ConsumerUsage) {
            $usage = $this->record;
            
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
                        // Direct structure (fallback)
                        $propertyPart = $item['property_part'] ?? 'Unknown';
                        $watt = floatval($item['watt'] ?? 0);
                        
                        if (!isset($propertyPartWattage[$propertyPart])) {
                            $propertyPartWattage[$propertyPart] = 0;
                        }
                        $propertyPartWattage[$propertyPart] += $watt;
                    }
                }
            }
        } else {
            // Add some test data if no specific record exists
            $propertyPartWattage = [
                'Kitchen' => 450,
                'Living Room' => 320,
                'Bedroom' => 180,
                'Bathroom' => 120,
                'Dining Room' => 95,
                'Office' => 85,
                'Garage' => 60,
            ];
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
        if ($this->record && $this->record->property) {
            $consumerInfo = ' - ' . $this->record->property->account_no . ' (' . $this->record->property->address . ')';
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
            'maintainAspectRatio' => true,
            'responsive' => true,
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Property Parts',
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Total Wattage (W)',
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
