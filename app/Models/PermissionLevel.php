<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionLevel extends Model
{
    protected $fillable = ['name', 'code'];


    protected $primaryKey = 'code';

 
    public $incrementing = false;



    public function users()
    {
        return $this->hasMany(User::class, 'permission_code', 'code');
    }
}
