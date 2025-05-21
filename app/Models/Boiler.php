<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boiler extends Model
{
    protected $fillable = [
        'power_plant_id',
        'name'
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
