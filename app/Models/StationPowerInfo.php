<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationPowerInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'date',
        'p',
        'p_max',
        'p_min',
        'produced_energy',      // Үйлдвэрлэсэн ЦЭХ
        'distributed_energy',   // Түгээсэн ЦЭХ
        'remark',
        'water_level',          // Усны төвшин /м/
        'fuel_amount',          // Түлшний нөөц /л/
    ];

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
