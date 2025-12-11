<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AltaiRegionCapacity extends Model
{
    protected $fillable = [
        'date',
        'max_load',
        'min_load',
        'import_from_bbexs',
        'import_from_tbns',
        'remark',
    ];
}
