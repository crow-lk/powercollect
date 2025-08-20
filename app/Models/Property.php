<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'consumer_id',
        'account_no',
        'address',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function parts()
    {
        return $this->hasMany(PropertyPart::class);
    }
}
