<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    protected $table = 'consumers';  
    protected $fillable = [
        'name',
        'account_no',
        'address',
        'nic'
    ];

    public function consumerUsages()
    {
        return $this->hasMany(ConsumerUsage::class);
    }
    
}
