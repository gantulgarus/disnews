<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlant extends Model
{
    protected $fillable = [
        'short_name',
        'name',
        'power_plant_type_id',
        'region',
        'z',
        't',
        'Order'
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function powerInfos()
    {
        return $this->hasMany(StationPowerInfo::class);
    }
    public function equipmentStatuses()
    {
        return $this->hasMany(EquipmentStatus::class);
    }
    public function powerPlantType()
    {
        return $this->belongsTo(PowerPlantType::class);
    }
}
