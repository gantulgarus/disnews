<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPowerHourReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_plant_id',
        'daily_power_equipment_id',
        'power_value',
        'date',
        'time',
        'user_id',
    ];

    // Харилцан холбоо (Relationships)
    public function equipment()
    {
        return $this->belongsTo(DailyPowerEquipment::class, 'daily_power_equipment_id');
    }

    public function powerPlant()
    {
        return $this->belongsTo(PowerPlant::class, 'power_plant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
