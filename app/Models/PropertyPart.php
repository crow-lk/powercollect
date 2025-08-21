<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyPart extends Model
{
    use HasFactory;

    protected $table = 'property_parts';

    protected $fillable = [
        'name',
    ];

    // public function property()
    // {
    //     return $this->belongsTo(Property::class);
    // }

    // public function equipment()
    // {
    //     return $this->hasMany(Equipment::class);
    // }

    // public function usages()
    // {
    //     return $this->hasMany(\App\Models\ConsumerUsage::class);
    // }
}
