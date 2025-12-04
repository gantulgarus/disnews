<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WesternRegionCapacity extends Model
{

    protected $fillable = [
        'p_max',
        'p_min',
        'p_imp_max',
        'p_imp_min',
        'import_received',
        'import_distributed',
        'date',
    ];
}
