<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderJournalStatusHistory extends Model
{
    protected $fillable = [
        'order_journal_id',
        'user_id',
        'old_status',
        'new_status',
        'comment',
    ];

    public function orderJournal(): BelongsTo
    {
        return $this->belongsTo(OrderJournal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
