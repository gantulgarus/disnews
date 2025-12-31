<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlantThermoEquipment extends Model
{
    protected $table = 'power_plant_thermo_equipments';

    protected $fillable = [
        'power_plant_id',
        'code',
        'name',
        'unit',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    public function readings()
    {
        return $this->hasMany(PowerPlantReading::class);
    }
}