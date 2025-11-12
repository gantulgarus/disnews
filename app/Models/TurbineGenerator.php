<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurbineGenerator extends Model
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
