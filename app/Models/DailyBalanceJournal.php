<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyBalanceJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'date',
        'processed_amount',
        'distribution_amount',
        'internal_demand',
        'percent',

        'positive_deviation_00_08',
        'negative_deviation_spot_00_08',
        'negative_deviation_import_00_08',
        'positive_resolution_00_08',
        'negative_resolution_00_08',

        'positive_deviation_08_16',
        'negative_deviation_spot_08_16',
        'negative_deviation_import_08_16',
        'positive_resolution_08_16',
        'negative_resolution_08_16',

        'positive_deviation_16_24',
        'negative_deviation_spot_16_24',
        'negative_deviation_import_16_24',
        'positive_resolution_16_24',
        'negative_resolution_16_24',

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