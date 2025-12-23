<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZConclusion extends Model
{
    // protected $connection = 'second_db'; // Хоёр дахь өгөгдлийн сантай холбогдоно
    protected $connection = 'mysql';   // Local DB-г заана (default)

    protected $table = 'z_conclusion'; // Хүснэгтийн нэр

    // protected $primaryKey = 'id'; // Анхдагч key

    public $timestamps = false; // created_at, updated_at байхгүй

    protected $fillable = [
        'PRJ',
        'ARV',
        'VAR',
        'CALCULATION',
        'TIMESTAMP_S',
        'TIMESTAMP_MS',
        'VALUE',
        'STATUS',
    ];

    protected $casts = [
        'CALCULATION' => 'integer',
        'TIMESTAMP_S' => 'integer',
        'TIMESTAMP_MS' => 'integer',
        'STATUS' => 'integer',
        'VALUE' => 'float', // хэрвээ тоон утга хадгалагддаг бол
    ];
}
