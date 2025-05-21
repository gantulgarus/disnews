<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlantDailyReport extends Model
{
    protected $fillable = [
        'power_plant_id',
        'report_date',
        'boiler_working',   // Ажилд байгаа зуухнууд
        'boiler_preparation', // Бэлтгэлд байгаа зуухнууд
        'boiler_repair',      // Засварт байгаа зуухнууд
        'turbine_working',   // Ажилд байгаа турбин
        'turbine_preparation', // Бэлтгэлд байгаа турбин
        'turbine_repair',      // Засварт байгаа турбин
        'notes',
        'power',
        'power_max'
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
