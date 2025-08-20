<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'name',
    ];

    // public function house()
    // {
    //     return $this->belongsTo(\App\Models\House::class);
    // }

    // public function equipment()
    // {
    //     return $this->hasMany(Equipment::class);
    // }

    // public function usages()
    // {
    //     return $this->hasMany(\App\Models\CustomerUsage::class);
    // }
}
