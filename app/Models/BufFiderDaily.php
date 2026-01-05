<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BufFiderDaily extends Model
{
    protected $table = 'buf_fider_daily';

    protected $fillable = [
        'OGNOO',
        'TIME_INTERVAL',
        'TIME_DISPLAY',
        'OBEKT',
        'SULJEE',
        'FIDER',
        'LINE_NAME',
        'IMPORT_KWT',
        'EXPORT_KWT',
        'TOOTSOOLUUR_COUNT',
    ];

    protected $casts = [
        'OGNOO' => 'date',
        'TIME_INTERVAL' => 'integer',
        'OBEKT' => 'integer',
        'SULJEE' => 'integer',
        'FIDER' => 'integer',
        'IMPORT_KWT' => 'decimal:2',
        'EXPORT_KWT' => 'decimal:2',
        'TOOTSOOLUUR_COUNT' => 'integer',
    ];

    /**
     * Тодорхой огнооны өгөгдөл
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('OGNOO', $date);
    }

    /**
     * Тодорхой фидер
     */
    public function scopeForFider($query, $fider)
    {
        return $query->where('FIDER', $fider);
    }

    /**
     * Олон фидер
     */
    public function scopeForFiders($query, array $fiders)
    {
        return $query->whereIn('FIDER', $fiders);
    }

    /**
     * Огнооны интервал
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('OGNOO', [$startDate, $endDate]);
    }

    /**
     * Pivot формат руу шилжүүлэх
     */
    public static function getPivotData($date, $fiders = [257, 258, 110])
    {
        $data = self::forDate($date)
            ->forFiders($fiders)
            ->selectRaw('
                TIME_DISPLAY,
                FIDER,
                SUM(IMPORT_KWT) as IMPORT_KWT,
                SUM(EXPORT_KWT) as EXPORT_KWT
            ')
            ->groupBy('TIME_INTERVAL', 'TIME_DISPLAY', 'FIDER')
            ->orderBy('TIME_INTERVAL')
            ->orderBy('FIDER')
            ->get();

        // Pivot үүсгэх
        $pivot = [];
        for ($i = 1; $i <= 48; $i++) {
            $hour = str_pad(floor(($i - 1) / 2), 2, '0', STR_PAD_LEFT);
            $startMin = ($i % 2 == 1) ? '00' : '30';
            $endMin = ($i % 2 == 1) ? '30' : '00';
            $timeKey = "{$hour}:{$startMin}-{$hour}:{$endMin}";

            $pivot[$timeKey] = [];
            foreach ($fiders as $fid) {
                $pivot[$timeKey][$fid] = ['IMPORT' => 0, 'EXPORT' => 0];
            }
        }

        foreach ($data as $row) {
            $time = $row->TIME_DISPLAY;
            $fid = (int)$row->FIDER;

            if (isset($pivot[$time][$fid])) {
                $pivot[$time][$fid]['IMPORT'] = (float)$row->IMPORT_KWT;
                $pivot[$time][$fid]['EXPORT'] = (float)$row->EXPORT_KWT;
            }
        }

        return $pivot;
    }
}
