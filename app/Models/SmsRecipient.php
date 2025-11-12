<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsRecipient extends Model
{
    protected $fillable = ['sms_group_id', 'name', 'phone'];

    public function group()
    {
        return $this->belongsTo(SmsGroup::class);
    }
}