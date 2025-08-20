<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerUsage extends Model
{
    protected $table = 'consumer_usages';

    protected $fillable = [
        'consumer_id',
        'equipment_id',
        'property_part_id',
        'kVA',
        'start_time',
        'end_time',
        'date',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'date' => 'date',
        'kVA' => 'decimal:2',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function propertyPart()
    {
        return $this->belongsTo(PropertyPart::class, 'property_part_id');
    }
}
