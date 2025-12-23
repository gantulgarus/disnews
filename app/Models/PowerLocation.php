<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerLocation extends Model
{
    protected $table = 'power_locations';

    protected $fillable = [
        'plant_name',
        'latitude',
        'longitude'
    ];
}
