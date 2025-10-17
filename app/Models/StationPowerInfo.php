<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationPowerInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'date',
        'p',
        'p_max',
        'p_min',
        'remark',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
