<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerUsage extends Model
{
    protected $table = 'customer_usages';

    protected $fillable = [
        'customer_id',
        'equipment_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
