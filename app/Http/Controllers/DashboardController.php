<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Regime;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Станцуудын бүлгүүд
    private $stationGroups = [
        'thermal' => [
            'name' => 'Дулааны цахилгаан станц',
            'stations' => [
                'PP4_TOTAL_P',
                'PP3_TOTAL_P',
                'PP2_TOTAL_P',
                'DARKHAN_PP_TOTAL_P',
                'ERDENET_PP_TOTAL_P',
                'GOK_PP_TOTAL_P',
                'DALANZADGAD_PP_TOTAL_P',
                'UHAAHUDAG_PP_TOTAL_P',
                'BUURULJUUT_TOTAL_P',
            ]
        ],
        'wind' => [
            'name' => 'Салхин цахилгаан станц',
            'stations' => [
                'SALKHIT_WPP_TOTAL_P',
                'TSETSII_WPP_TOTAL_P',
                'SHAND_WPP_TOTAL_P',
            ]
        ],
        'solar' => [
            'name' => 'Нарны цахилгаан станц',
            'stations' => [
                'DARKHAN_SPP_TOTAL_P',
                'MONNAR_SPP_TOTAL_P',
                'GEGEEN_SPP_TOTAL_P',
                'SUMBER_SPP_TOTAL_P',
                'BUHUG_SPP_TOTAL_P',
                'GOVI_SPP_TOTAL_P',
                'ERDENE_SPP_TOTAL_P'
            ]
        ],
        'battery' => [
            'name' => 'Батарей хуримтлуур',
            'stations' => [
                'ERDENE_SPP_BHB_TOTAL_P',
                'BAGANUUR_BESS_TOTAL_P_T',
                'SONGINO_BESS_TOTAL_P'
            ]
        ],
        'import' => [
            'name' => 'Импорт',
            'stations' => [
                'IMPORT_EXPORT_TOTAL_P'
            ]
        ]
    ];

    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        // Хуудсыг шууд харуулах, мэдээллийг JavaScript-ээр ачаална
        return view('dashboard', compact('date'));
    }

    // OPTIMIZED: Real-time мэдээлэл авах
    public function realtimeData(Request $request)
    {
        try {
            $currentDate = now()->toDateString();

            // Calculate timestamp range for today
            $startTimestamp = Carbon::parse($currentDate)->startOfDay()->timestamp;
            $endTimestamp = Carbon::parse($currentDate)->endOfDay()->timestamp;

            // Get all station names
            $allStations = collect($this->stationGroups)->flatMap(function ($group) {
                return $group['stations'];
            })->toArray();

            // OPTIMIZED: Use window function to get latest record per VAR
            // This is MUCH faster than the subquery approach
            // $latestData = DB::table(DB::raw('(
            //     SELECT
            //         VAR,
            //         VALUE,
            //         TIMESTAMP_S,
            //         ROW_NUMBER() OVER (PARTITION BY VAR ORDER BY TIMESTAMP_S DESC) as rn
            //     FROM Z_Conclusion
            //     WHERE VAR IN (' . implode(',', array_map(fn($s) => "'$s'", $allStations)) . ')
            //         AND TIMESTAMP_S BETWEEN ' . $startTimestamp . ' AND ' . $endTimestamp . '
            //         AND CALCULATION = 50
            // ) as ranked'))
            //     ->where('rn', 1)
            //     ->get()
            //     ->keyBy('VAR');

            // Alternative approach if window functions are not available:
            // Use a simple query with ORDER BY and LIMIT grouped results
            $latestData = collect($allStations)->mapWithKeys(function ($station) use ($startTimestamp, $endTimestamp) {
                $record = ZConclusion::select('VAR', 'VALUE', 'TIMESTAMP_S')
                    ->where('VAR', $station)
                    ->whereBetween('TIMESTAMP_S', [$startTimestamp, $endTimestamp])
                    ->where('CALCULATION', 50)
                    ->orderByDesc('TIMESTAMP_S')
                    ->first();

                return $record ? [$station => $record] : [];
            });

            // Most recent timestamp
            $latestTimestamp = $latestData->isNotEmpty()
                ? $latestData->max('TIMESTAMP_S')
                : null;

            // Calculate sums by group
            $typeSums = [];

            foreach ($this->stationGroups as $key => $group) {
                $sumP = 0;

                foreach ($group['stations'] as $station) {
                    if (isset($latestData[$station])) {
                        $sumP += (float)$latestData[$station]->VALUE;
                    }
                }

                $typeSums[] = [
                    'type_name' => $group['name'],
                    'sumP' => $sumP,
                ];
            }

            // Get latest system_total_p value - OPTIMIZED
            $systemTotal = ZConclusion::where('VAR', 'system_total_p')
                ->whereBetween('TIMESTAMP_S', [$startTimestamp, $endTimestamp])
                ->where('CALCULATION', 50)
                ->orderByDesc('TIMESTAMP_S')
                ->first();

            $totalP = $systemTotal ? (float)$systemTotal->VALUE : 0;

            return response()->json([
                'success' => true,
                'typeSums' => $typeSums,
                'totalP' => $totalP,
                'latestTimestamp' => $latestTimestamp,
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard realtime data error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Холболтын алдаа гарлаа'
            ], 500);
        }
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

            // OPTIMIZED: Get ZConclusion data using timestamp directly
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

            // Find peak load
            $peakLoad = [
                'time' => null,
                'value' => null,
                'source' => null
            ];

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
            Log::error('Dashboard data error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Холболтын алдаа гарлаа'
            ], 500);
        }
    }
}