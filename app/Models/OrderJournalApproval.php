<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderJournalApproval extends Model
{
    use HasFactory;

    protected $fillable = ['order_journal_id', 'user_id', 'approved', 'comment'];

    public function orderJournal()
    {
        return $this->belongsTo(OrderJournal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
