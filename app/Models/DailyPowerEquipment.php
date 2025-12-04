<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPowerEquipment extends Model
{
    use HasFactory;

    protected $table = 'daily_power_equipments'; // make sure table name matches your migration

    protected $fillable = [
        'power_plant_id',
        'power_equipment',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
