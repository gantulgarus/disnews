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
                'BUURULJUUT_PP_TOTAL_P',

                // 'TOSON_PP_TOTAL_P',
                // 'DORNOD_PP_TOTAL_P'
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
            'name' => 'Батарэй хуримтлуур',
            'stations' => [
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

        try {
            // Get real-time data from ZConclusion
            $currentDate = now()->toDateString();

            // Get all station names
            $allStations = collect($this->stationGroups)->flatMap(function ($group) {
                return $group['stations'];
            })->toArray();

            // Get latest values for all stations
            $latestData = ZConclusion::select('VAR', 'VALUE', 'TIMESTAMP_S')
                ->whereIn('VAR', $allStations)
                ->whereDate(DB::raw('FROM_UNIXTIME(TIMESTAMP_S)'), $currentDate)
                ->where('CALCULATION', 50)
                ->whereIn('TIMESTAMP_S', function ($query) use ($currentDate, $allStations) {
                    $query->select(DB::raw('MAX(TIMESTAMP_S)'))
                        ->from('Z_Conclusion')
                        ->whereIn('VAR', $allStations)
                        ->whereDate(DB::raw('FROM_UNIXTIME(TIMESTAMP_S)'), $currentDate)
                        ->where('CALCULATION', 50)
                        ->groupBy('VAR');
                })
                ->get()
                ->keyBy('VAR');

            // ➤ MOST RECENT TIMESTAMP (this is what you need)
            $latestTimestamp = $latestData->isNotEmpty()
                ? $latestData->max('TIMESTAMP_S')
                : null;

            // Calculate sums by group
            $typeSums = [];
            $totalP = 0;

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

                $totalP += $sumP;
            }


            return view('dashboard', compact('date', 'typeSums', 'totalP', 'latestTimestamp'));
        } catch (\Exception $e) {
            Log::error('Dashboard index error: ' . $e->getMessage());

            // Fallback to empty data
            $typeSums = [];
            $totalP = 0;

            return view('dashboard', compact('date', 'typeSums', 'totalP'));
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

            // Get ZConclusion data
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
