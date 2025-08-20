<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_no',
        'address',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function parts()
    {
        return $this->hasMany(HousePart::class);
    }
}
