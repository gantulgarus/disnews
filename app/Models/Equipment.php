<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'power_plant_id',
        'equipment_type_id',
        'name',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    public function statuses()
    {
        return $this->hasMany(EquipmentStatus::class, 'equipment_id');
    }

    public function type()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }
}
