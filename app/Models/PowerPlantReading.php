<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlantReading extends Model
{
    protected $fillable = [
        'power_plant_thermo_equipment_id',
        'reading_date',
        'reading_hour',
        'value',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'reading_hour' => 'integer',
        'value' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->belongsTo(PowerPlantThermoEquipment::class, 'power_plant_thermo_equipment_id');
    }
}