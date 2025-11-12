<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    protected $fillable = ['sms_group_id', 'message', 'recipients_count', 'sent_at', 'group_ids'];

    protected $casts = [
        'group_ids' => 'array',
        'sent_at' => 'datetime',
    ];


    public function group()
    {
        return $this->belongsTo(SmsGroup::class, 'sms_group_id');
    }

    public function groups()
    {
        return SmsGroup::whereIn('id', $this->group_ids ?? []);
    }
}