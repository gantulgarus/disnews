<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBalanceImportExport extends Model
{
    protected $fillable = [
        'power_plant_id',
        'date',
        'import',
        'export',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
