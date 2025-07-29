<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regime extends Model
{
    protected $connection = 'second_db';

    protected $table = 'regime'; // хүснэгтийн нэр

    protected $primaryKey = 'id'; // анхдагч key

    public $timestamps = false; // created_at, updated_at байхгүй

    protected $fillable = [
        'TS_NR',
        'DATE',
        'VALUE',
    ];

    protected $casts = [
        'DATE' => 'datetime',
        'VALUE' => 'float',
    ];
}
