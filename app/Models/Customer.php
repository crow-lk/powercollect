<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    protected $table = 'customers';  
    protected $fillable = [
        'name',
        'account_no',
        'address',
        'nic'
    ];

    public function customerUsages()
    {
        return $this->hasMany(CustomerUsage::class);
    }
    
}


