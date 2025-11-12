<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelephoneMessage extends Model
{
    protected $fillable = [
        'status',
        'sender_org_id',
        'receiver_org_ids',
        'content',
        'attachment',
        'created_user_id',
    ];

    protected $casts = [
        'receiver_org_ids' => 'array',
    ];

    public function receivers()
    {
        return $this->belongsToMany(Organization::class, 'telephone_message_receiver')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function senderOrganization()
    {
        return $this->belongsTo(Organization::class, 'sender_org_id');
    }
}
