<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regime extends Model
{
    protected $connection = 'second_db';

    protected $table = 'w_news'; // хүснэгтийн нэр

    protected $primaryKey = 'id'; // анхдагч key

    public $timestamps = false; // created_at, updated_at байхгүй

    protected $fillable = [
        'date',
        'text',
        'tmax',
        'tmin',
        'plant_name',
        'Pmax',
        'Pmin',
        'zuuh',
        'TG',
        't1',
        't2',
        't3',
        't4',
        't5',
        't6',
        't7',
        't8',
        't9',
        't10',
        't11',
        't12',
        't13',
        't14',
        't15',
        't16',
        't17',
        't18',
        't19',
        't20',
        't21',
        't22',
        't23',
        't24',
    ];
}