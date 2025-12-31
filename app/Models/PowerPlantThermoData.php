<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlantThermoData extends Model
{
    protected $table = 'power_plant_thermo_data';

    protected $fillable = [
        'infodate',
        'infotime',
        'power_plant_id',
        'equipment_code',
        'value',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    public function equipment()
    {
        return $this->belongsTo(PowerPlantThermoEquipment::class, 'equipment_code', 'code');
    }
}
