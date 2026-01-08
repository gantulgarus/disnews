<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
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
    const STATUS_IN_REVIEW = 10;

    public static array $STATUS_NAMES = [
        self::STATUS_NEW => 'Шинэ',
        self::STATUS_SENT => 'Илгээсэн',
        self::STATUS_FORWARDED => 'Бусад албаруу илгээсэн',
        self::STATUS_APPROVED => 'Батлагдсан',
        self::STATUS_CANCELLED => 'Цуцлагдсан',
        self::STATUS_SENT_TO_GENERAL => 'Ерөнхий диспетчерт илгээгдсэн',
        self::STATUS_ACCEPTED => 'Зөвшөөрсөн',
        self::STATUS_OPEN => 'Нээлттэй',
        self::STATUS_CLOSED => 'Хаалттай',
        self::STATUS_POSTPONED => 'Хойшлогдсон',
        self::STATUS_IN_REVIEW => 'Хянагдаж байгаа',
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
        'dut_dispatcher_id',
        'tze_dis_name'
    ];

    protected $casts = [
        'planned_start_date' => 'datetime',
        'planned_end_date' => 'datetime',
        'real_start_date' => 'datetime',
        'real_end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($journal) {
            // Анх status = STATUS_NEW
            $journal->status = self::STATUS_NEW;

            // created_user_id, organization_id-г хэрэглэгчээс авах (если Auth байна гэж үзвэл)
            if (auth()->check()) {
                $journal->created_user_id = auth()->id();

                // Админ биш бол зөвхөн өөрийн байгууллага
                if (auth()->user()->permissionLevel?->code !== 'DISP') {
                    $journal->organization_id = auth()->user()->organization_id;
                }
            }

            // Автомат order_number
            $lastOrder = DB::table('order_journals')->lockForUpdate()->latest('id')->first();
            $journal->order_number = $lastOrder ? $lastOrder->order_number + 1 : 1;
        });
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function dutDispatcher()
    {
        return $this->belongsTo(User::class, 'dut_dispatcher_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function approvals()
    {
        return $this->hasMany(OrderJournalApproval::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderJournalStatusHistory::class);
    }
}
