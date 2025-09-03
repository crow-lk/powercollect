<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerUsage extends Model
{
    protected $table = 'consumer_usages';

    protected $fillable = [
        'property_id',
        'date',
        'usage_data',
    ];

    protected $casts = [
        'date' => 'date',
        'usage_data' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    // Helper method to get formatted equipment details from usage_data
    public function getEquipmentDetailsAttribute()
    {
        if (is_array($this->usage_data)) {
            return collect($this->usage_data)->map(function ($item) {
                // Handle both old and new structure
                if (isset($item['equipment_data'])) {
                    // New nested structure
                    return collect($item['equipment_data'])->map(function ($equipment) use ($item) {
                        $timePeriods = is_array($equipment['time_period'] ?? null)
                            ? $equipment['time_period']
                            : [$equipment['time_period'] ?? 'N/A'];

                        return [
                            'equipment' => $equipment['equipment'] ?? 'N/A',
                            'watt' => $equipment['watt'] ?? 0,
                            'time_periods' => $timePeriods,
                            'property_part' => $item['property_part'] ?? 'N/A',
                        ];
                    });
                } else {
                    // Direct structure
                    $timePeriods = is_array($item['time_period'] ?? null)
                        ? $item['time_period']
                        : [$item['time_period'] ?? 'N/A'];

                    return collect([[
                        'equipment' => $item['equipment'] ?? 'N/A',
                        'watt' => $item['watt'] ?? 0,
                        'time_periods' => $timePeriods,
                        'property_part' => $item['property_part'] ?? 'N/A',
                    ]]);
                }
            })->flatten(1);
        }

        return collect();
    }


    public function getTotalEquipmentCountAttribute()
    {
        if (is_array($this->usage_data)) {
            return collect($this->usage_data)->sum(function ($item) {
                if (isset($item['equipment_data'])) {
                    // New nested structure
                    return count($item['equipment_data']);
                } else {
                    // Direct structure
                    return 1;
                }
            });
        }

        return 0;
    }

    // Helper method to get total time slots count across all equipment
    public function getTotalTimeSlotsCountAttribute()
    {
        if (is_array($this->usage_data)) {
            return collect($this->usage_data)->sum(function ($item) {
                if (isset($item['equipment_data'])) {
                    // New nested structure
                    return collect($item['equipment_data'])->sum(function ($equipment) {
                        $timePeriods = is_array($equipment['time_period'] ?? null)
                            ? $equipment['time_period']
                            : [$equipment['time_period'] ?? null];

                        return count(array_filter($timePeriods));
                    });
                } else {
                    // Direct structure
                    $timePeriods = is_array($item['time_period'] ?? null)
                        ? $item['time_period']
                        : [$item['time_period'] ?? null];

                    return count(array_filter($timePeriods));
                }
            });
        }

        return 0;
    }

    // Helper method to get unique equipment used
    public function getUniqueEquipmentAttribute()
    {
        if (is_array($this->usage_data)) {
            return collect($this->usage_data)
                ->pluck('equipment_name')
                ->unique()
                ->values();
        }

        return collect();
    }

    // Helper method to get time slots by equipment
    public function getTimeSlotsByEquipmentAttribute()
    {
        if (is_array($this->usage_data)) {
            return collect($this->usage_data)->mapWithKeys(function ($equipmentRecord) {
                $equipmentName = $equipmentRecord['equipment_name'] ?? 'Unknown Equipment';

                return [$equipmentName => collect($equipmentRecord['time_slots'] ?? [])];
            });
        }

        return collect();
    }
}
