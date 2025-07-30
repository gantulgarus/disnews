<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderJournal extends Model
{
    use HasFactory;

    const STATUS_NEW = 0;
    const STATUS_SENT = 1;
    const STATUS_FORWARDED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_SENT_TO_GENERAL = 5;
    const STATUS_ACCEPTED = 6;
    const STATUS_OPEN = 7;
    const STATUS_CLOSED = 8;
    const STATUS_POSTPONED = 9;

    public static array $STATUS_NAMES = [
        self::STATUS_NEW => 'Шинэ',
        self::STATUS_SENT => 'Илгээсэн',
        self::STATUS_FORWARDED => 'Бусад алба руу илгээсэн',
        self::STATUS_APPROVED => 'Баталгаажсан',
        self::STATUS_CANCELLED => 'Цуцлагдсан',
        self::STATUS_SENT_TO_GENERAL => 'Ерөнхий диспетчерт илгээгдсэн',
        self::STATUS_ACCEPTED => 'Зөвшөөрсөн',
        self::STATUS_OPEN => 'Нээлттэй',
        self::STATUS_CLOSED => 'Хаалттай',
        self::STATUS_POSTPONED => 'Хойшлогдсон',
    ];

    protected $fillable = [
        'order_number',
        'status',
        'organization_id',
        'order_type',
        'content',
        'planned_start_date',
        'planned_end_date',
        'approver_name',
        'approver_position',
        'real_start_date',
        'real_end_date',
        'created_user_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}