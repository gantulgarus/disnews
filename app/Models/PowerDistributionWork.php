<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PowerDistributionWork extends Model
{
    use HasFactory;

    protected $table = 'power_distribution_works'; // хүснэгтийн нэр

    protected $fillable = [
        'tze',                // ТЗЭ
        'repair_work',        // Засварын ажлын утга
        'description',        // Тайлбар
        'restricted_energy',  // Хязгаарласан эрчим хүч
        'date',               // Огноо
        'user_id',            // Холбогдсон хэрэглэгч
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
