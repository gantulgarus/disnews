<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerEnergyAdjustment extends Model
{
    protected $fillable = [
        'restricted_kwh',
        'discounted_kwh',
        'date',
    ];

    // Carbon объект болгон хөрвүүлэх
    protected $casts = [
        'date' => 'date',
    ];
}
