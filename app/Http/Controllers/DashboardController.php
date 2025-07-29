<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Regime;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Set time range from 01:00:00 of selected date to 00:00:00 of next day
        $startDate = Carbon::parse($date)->startOfDay()->addHour(); // 01:00:00
        $endDate = Carbon::parse($date)->addDay()->startOfDay();    // 00:00:00 next day

        // Get Regime data
        $regimeData = Regime::select('DATE', 'VALUE')
            ->where('TS_NR', 31198)
            ->whereBetween('DATE', [$startDate, $endDate])
            ->orderBy('DATE')
            ->get();

        // Get ZConclusion data with fixed GROUP BY clause
        $zconclusionData = ZConclusion::select(
            DB::raw('HOUR(FROM_UNIXTIME(timestamp_s)) as hour_num'),
            DB::raw('FROM_UNIXTIME(timestamp_s, "%H:00") as hour'),
            DB::raw('AVG(CAST(VALUE AS DECIMAL(10,2))) as value')
        )
            ->where('VAR', 'system_total_p')
            ->where('calculation', 50)
            ->whereBetween('timestamp_s', [
                $startDate->timestamp,
                $endDate->timestamp
            ])
            ->groupBy('hour_num', 'hour')
            ->orderBy('hour_num')
            ->get();

        // Generate complete 24-hour timeline (01:00 to 00:00)
        $allHours = collect();
        for ($i = 1; $i <= 24; $i++) {
            $hour = $i % 24;
            $allHours->push(str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00');
        }

        // Map regime data
        $regimeMap = $regimeData->mapWithKeys(function ($item) {
            return [Carbon::parse($item->DATE)->format('H:00') => (float)$item->VALUE];
        });

        // Map zconclusion data
        $zconclusionMap = $zconclusionData->mapWithKeys(function ($item) {
            return [$item->hour => (float)$item->value];
        });

        // Prepare values for all hours
        $regimeValues = $allHours->map(function ($hour) use ($regimeMap) {
            return $regimeMap->get($hour);
        });

        $zconclusionValues = $allHours->map(function ($hour) use ($zconclusionMap) {
            return $zconclusionMap->get($hour);
        });

        // Find peak load (maximum value) and its time
        $peakLoad = [
            'time' => null,
            'value' => null,
            'source' => null
        ];

        // Find peak in Regime data
        // if ($regimeValues->filter()->isNotEmpty()) {
        //     $regimePeakValue = $regimeValues->max();
        //     $regimePeakTime = $allHours[$regimeValues->search($regimePeakValue)];

        //     if ($peakLoad['value'] === null || $regimePeakValue > $peakLoad['value']) {
        //         $peakLoad = [
        //             'time' => $regimePeakTime,
        //             'value' => $regimePeakValue,
        //             'source' => 'Regime'
        //         ];
        //     }
        // }

        // Find peak in ZConclusion data
        if ($zconclusionValues->filter()->isNotEmpty()) {
            $zconclusionPeakValue = $zconclusionValues->max();
            $zconclusionPeakTime = $allHours[$zconclusionValues->search($zconclusionPeakValue)];

            if ($peakLoad['value'] === null || $zconclusionPeakValue > $peakLoad['value']) {
                $peakLoad = [
                    'time' => $zconclusionPeakTime,
                    'value' => $zconclusionPeakValue,
                    'source' => 'ZConclusion'
                ];
            }
        }

        // Format peak value if exists
        if ($peakLoad['value'] !== null) {
            $peakLoad['formatted_value'] = number_format($peakLoad['value'], 2);
        }

        return view('dashboard', [
            'date' => $date,
            'times' => $allHours,
            'regimeValues' => $regimeValues,
            'zconclusionValues' => $zconclusionValues,
            'peakLoad' => $peakLoad
        ]);
    }
}
