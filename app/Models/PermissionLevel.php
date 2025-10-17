<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionLevel extends Model
{
    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->hasMany(User::class, 'permission_level_id');
    }
}
