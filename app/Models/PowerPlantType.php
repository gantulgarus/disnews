<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlantType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function powerPlants()
    {
        return $this->hasMany(PowerPlant::class);
    }
}
