<?php

namespace App\Services;

use App\Models\RuFiderDaily;
use Carbon\Carbon;

class RussianXmlImportService
{
    public static function import(string $xmlPath, array $fiderMap)
    {
        $xml = simplexml_load_file($xmlPath);

        $day = (string)$xml->datetime->day; // 20251201
        $date = Carbon::createFromFormat('Ymd', $day)->toDateString();

        foreach ($xml->area->measuringpoint as $point) {
            $code = (string)$point['code'];

            if (!isset($fiderMap[$code])) {
                continue;
            }

            $fider = $fiderMap[$code];

            foreach ($point->measuringchannel as $channel) {
                $type = (string)$channel['desc']; // АП / АО
                $interval = 1; // period-ийн index-г тоолоход

                foreach ($channel->period as $period) {
                    $start = (string)$period['start']; // 0000
                    $time = substr($start, 0, 2) . ':' . substr($start, 2, 2);
                    $value = (float)$period->value;

                    $row = RuFiderDaily::firstOrNew([
                        'ognoo' => $date,
                        'time_interval' => $interval,
                        'fider' => $fider,
                    ]);

                    $row->time_display = $time;

                    if ($type === 'АП') {
                        $row->import_kwt = $value;
                    } elseif ($type === 'АО') {
                        $row->export_kwt = $value;
                    }

                    $row->save();

                    $interval++; // дараагийн period
                }
            }
        }
    }
}
