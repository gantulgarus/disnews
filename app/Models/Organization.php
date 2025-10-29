<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'org_code'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function powerPlants()
    {
        return $this->hasMany(PowerPlant::class);
    }
}
