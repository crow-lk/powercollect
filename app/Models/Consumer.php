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

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('nic', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%")
                ->orWhereHas('properties', function ($propertyQuery) use ($search) {
                    $propertyQuery->where('account_no', 'like', "%{$search}%");
                });
        });
    }
    
}
