<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsGroup extends Model
{
    protected $fillable = ['name'];

    public function recipients()
    {
        return $this->hasMany(SmsRecipient::class);
    }
}