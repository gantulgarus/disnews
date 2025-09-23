<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Regime;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard'); // зөвхөн layout ачаална
    }

    public function data(Request $request)
    {
        try {
            $date = $request->input('date', now()->toDateString());

            // Set time range from 01:00:00 of selected date to 00:00:00 of next day
            $startDate = Carbon::parse($date)->startOfDay()->addHour(); // 01:00:00
            $endDate = Carbon::parse($date)->addDay()->startOfDay();    // 00:00:00 next day

            // Get Regime data
            $regimeRecord = Regime::where('plant_name', 'Хэрэглээ')
                ->whereDate('date', $date)
                ->first();

            $regimeValues = collect();

            if ($regimeRecord) {
                for ($i = 1; $i <= 24; $i++) {
                    $regimeValues->push($regimeRecord->{'t' . $i});
                }
            }


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

            // Map zconclusion data
            $zconclusionMap = $zconclusionData->mapWithKeys(function ($item) {
                return [$item->hour => (float)$item->value];
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

            return response()->json([
                'success' => true,
                'date' => $date,
                'times' => $allHours,
                'regimeValues' => $regimeValues,
                'zconclusionValues' => $zconclusionValues,
                'peakLoad' => $peakLoad,
            ]);
        } catch (\Exception $e) {
            Log::error('Second DB connection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Холболтын алдаа гарлаа'
            ], 500);
        }
    }
}
