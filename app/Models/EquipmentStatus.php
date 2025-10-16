<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'equipment_id',
        'date',
        'status',
        'remark',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
