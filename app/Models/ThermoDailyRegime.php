<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThermoDailyRegime extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'date',
        'time_range',
        'temperature',
        't1',
        't2',
        'p1',
        'p2',
        'd',
        'g',
        'q',
        'q_total',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
