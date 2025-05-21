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
