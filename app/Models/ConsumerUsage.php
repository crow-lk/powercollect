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
}
