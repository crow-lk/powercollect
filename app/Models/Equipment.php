<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
        'type',
        'brand',
        'model',
        'kVA'
    ];
}
