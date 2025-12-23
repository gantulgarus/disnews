<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisCoal extends Model
{
    use HasFactory;

    protected $table = 'dis_coal';

    protected $fillable = [
        'date',
        'CAME_TRAIN',
        'UNLOADING_TRAIN',
        'ULDSEIN_TRAIN',
        'COAL_INCOME',
        'COAL_OUTCOME',
        'COAL_TRAIN_QUANTITY',
        'COAL_REMAIN',
        'COAL_REMAIN_BYDAY',
        'COAL_REMAIN_BYWINTERDAY',
        'MAZUT_INCOME',
        'MAZUT_OUTCOME',
        'MAZUT_TRAIN_QUANTITY',
        'MAZUT_REMAIN',
        'BAGANUUR_MINING_COAL_D',
        'SHARINGOL_MINING_COAL_D',
        'SHIVEEOVOO_MINING_COAL',
        'OTHER_MINIG_COAL_SUPPLY',
        'FUEL_SENDING_EMPL',
        'FUEL_RECEIVER_EMPL',
        'ORG_CODE',
        'ORG_NAME',
        'power_plant_id',
    ];

    public function powerPlant()
    {
        return $this->belongsTo(\App\Models\PowerPlant::class);
    }
}