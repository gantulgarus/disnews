<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZConclusionFiltered extends Model
{
    protected $connection = 'second_db'; // Хэрвээ Z_Conclusion-той нэг өгөгдлийн сан бол энэ хэвээр
    protected $table = 'z_conclusion_filtered';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'PRJ',
        'ARV',
        'VAR',
        'CALCULATION',
        'TIMESTAMP_S',
        'VALUE',
        'STATUS'
    ];

    protected $casts = [
        'CALCULATION' => 'integer',
        'TIMESTAMP_S' => 'integer',
        'STATUS' => 'integer',
        'VALUE' => 'float',
    ];
}
