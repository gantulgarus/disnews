<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuFiderDaily extends Model
{
    protected $table = 'ru_fider_daily';

    protected $fillable = [
        'ognoo',
        'time_display',
        'time_interval',
        'fider',
        'import_kwt',
        'export_kwt',
    ];

    protected $casts = [
        'ognoo' => 'date',
    ];
}