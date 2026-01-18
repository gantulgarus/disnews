<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectricDailyRegime extends Model
{
    use HasFactory;

    protected $table = 'electric_daily_regimes';

    protected $fillable = [
        'power_plant_id',
        'user_id',
        'date',
        'technical_pmax',
        'technical_pmin',
        'pmax',
        'pmin',
        'hour_1',
        'hour_2',
        'hour_3',
        'hour_4',
        'hour_5',
        'hour_6',
        'hour_7',
        'hour_8',
        'hour_9',
        'hour_10',
        'hour_11',
        'hour_12',
        'hour_13',
        'hour_14',
        'hour_15',
        'hour_16',
        'hour_17',
        'hour_18',
        'hour_19',
        'hour_20',
        'hour_21',
        'hour_22',
        'hour_23',
        'hour_24',
        'total_mwh',
        'note',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Хэрэв хүсвэл харилцаа үүсгэж болно
    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class, 'power_plant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
