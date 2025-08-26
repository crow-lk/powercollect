<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory;
    protected $table = 'equipments';

    protected $fillable = [
        'type',
        'brand',
        'model',
        'watt'
    ];

    // public function consumerUsages()
    // {
    //     return $this->hasMany(ConsumerUsage::class);
    // }
}
