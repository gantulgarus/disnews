<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyBalanceJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'entry_date_time',
        'time_range',
        'processed_amount',
        'distribution_amount',
        'internal_demand',
        'percent',
        'positive_deviation',
        'negative_deviation_spot',
        'negative_deviation_import',
        'positive_resolution',
        'negative_resolution',
        'deviation_reason',
        'by_consumption_growth',
        'by_other_station_issue',
        'dispatcher_name',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
