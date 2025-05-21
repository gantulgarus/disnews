<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlant extends Model
{
    protected $fillable = [
        'short_name',
        'name',
        'z',
        't'
    ];

    public function boilers()
    {
        return $this->hasMany(Boiler::class);
    }

    public function turbineGenerators()
    {
        return $this->hasMany(TurbineGenerator::class);
    }
    public function dailyReports()
    {
        return $this->hasMany(PowerPlantDailyReport::class);
    }
}
