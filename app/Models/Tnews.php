<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tnews extends Model
{
    protected $fillable = [
        'date',
        'time',
        'TZE',
        'tasralt',
        'ArgaHemjee',
        'HyzErchim',
        'send_telegram',
    ];

    protected $casts = [
        'send_telegram' => 'boolean',
    ];
}
