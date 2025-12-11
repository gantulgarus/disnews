<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBalanceBattery extends Model
{
    protected $fillable = [
        'power_plant_id',
        'date',
        'energy_given',
        'energy_taken',
    ];
    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
