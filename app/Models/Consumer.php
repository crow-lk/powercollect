<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    use HasFactory;
    protected $table = 'consumers';  
    protected $fillable = [
        'name',
        'address',
        'nic'
    ];

    public function consumerUsages()
    {
        return $this->hasMany(ConsumerUsage::class);
    }
    
}
