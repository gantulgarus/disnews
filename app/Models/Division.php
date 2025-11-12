<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['Div_name', 'Div_code'];
    protected $primaryKey = 'Div_code';
    public $incrementing = false;


    public function users()
    {
        return $this->hasMany(User::class, 'div_code', 'div_code');
    }

}

