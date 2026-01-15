<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastData extends Model
{
    protected $table = 'forecast_data';

    protected $fillable = [
        'time',
        'actual_load',
        'daily_forecast',
        'hourly_forecast',
        'is_actual',
        'forecast_type'
    ];

    protected $casts = [
        'time' => 'datetime',
        'actual_load' => 'float',
        'daily_forecast' => 'float',
        'hourly_forecast' => 'float',
        'is_actual' => 'boolean',
    ];
}
