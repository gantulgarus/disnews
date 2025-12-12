<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Regime;
use App\Models\ZConclusion;
use App\Models\ElectricDailyRegime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Хэрэглэгчийн байгууллагын станцуудын VAR кодуудыг авах
     */
    private function getUserStationVars()
    {
        $user = Auth::user();

        // Хэрэв байгууллагын мэдээлэл байхгүй бол бүх станцыг харуулна
        if (!$user->organization_id) {
            return null;
        }

        // Хэрэглэгчийн байгууллагын станцуудыг авах (үндсэн болон дэд станцууд)
        $powerPlants = $user->organization->powerPlants;

        if ($powerPlants->isEmpty()) {
            return null;
        }

        // Станцуудын short_name-уудыг цуглуулах
        // $stationShortNames = $powerPlants->pluck('short_name')->toArray();

        // VAR кодууд үүсгэх (жишээ: PP4 -> PP4_TOTAL_P)
        // $userStationVars = array_map(function ($shortName) {
        //     return $shortName . '_TOTAL_P';
        // }, $stationShortNames);
        $userStationVars = $powerPlants->pluck('short_name')->toArray();
        // ['PP4_TOTAL_P', 'PP3_TOTAL_P'] шууд ийм ирнэ

        return $userStationVars;
    }

    /**
     * Хэрэглэгчийн эрхээр станцуудыг шүүх
     */
    private function filterStationGroups()
    {
        $userStationVars = $this->getUserStationVars();

        // Хэрэв null бол бүгдийг харуулна (системийн админ эсвэл тусгай эрх)
        if ($userStationVars === null) {
            return $this->stationGroups;
        }

        // Хэрэглэгчийн станцуудаар шүүх
        $filtered = [];

        foreach ($this->stationGroups as $key => $group) {
            $filteredStations = array_intersect($group['stations'], $userStationVars);

            if (!empty($filteredStations)) {
                $filtered[$key] = [
                    'name' => $group['name'],
                    'stations' => array_values($filteredStations)
                ];
            }
        }

        return $filtered;
    }

    /**
     * Хэрэглэгчийн үндсэн станцыг авах
     */
    private function getUserMainPowerPlant()
    {
        $user = Auth::user();

        if (!$user->organization_id) {
            return null;
        }

        // Байгууллагын үндсэн станцыг авах (parent_id = null)
        return $user->organization->powerPlants()
            ->whereNull('parent_id')
            ->first();
    }

    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        // Хэрэглэгчийн эрхээр шүүсэн станцуудыг дамжуулах
        $stationGroups = $this->filterStationGroups();
        $isSystemView = $this->getUserStationVars() === null;

        return view('dashboard', compact('date', 'stationGroups', 'isSystemView'));
    }

    public function realtimeData(Request $request)
    {
        try {
            $currentDate = now()->toDateString();
            $startTimestamp = Carbon::parse($currentDate)->startOfDay()->timestamp;
            $endTimestamp = Carbon::parse($currentDate)->endOfDay()->timestamp;

            // Хэрэглэгчийн эрхээр шүүсэн станцууд
            $stationGroups = $this->filterStationGroups();
            $isSystemView = $this->getUserStationVars() === null;

            // Бүх станцуудын жагсаалт
            $allStations = collect($stationGroups)->flatMap(function ($group) {
                return $group['stations'];
            })->toArray();

            if (empty($allStations)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Таны эрх байхгүй байна'
                ], 403);
            }

            // Сүүлийн мэдээллийг авах
            $latestData = collect($allStations)->mapWithKeys(function ($station) use ($startTimestamp, $endTimestamp) {
                $record = ZConclusion::select('VAR', 'VALUE', 'TIMESTAMP_S')
                    ->where('VAR', $station)
                    ->whereBetween('TIMESTAMP_S', [$startTimestamp, $endTimestamp])
                    ->where('CALCULATION', 50)
                    ->orderByDesc('TIMESTAMP_S')
                    ->first();

                return $record ? [$station => $record] : [];
            });

            $latestTimestamp = $latestData->isNotEmpty()
                ? $latestData->max('TIMESTAMP_S')
                : null;

            // Бүлгээр нийлбэр тооцоолох
            $typeSums = [];

            foreach ($stationGroups as $key => $group) {
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

            // Нийт чадал авах
            // Хэрэв системийн харагдац бол system_total_p ашиглах
            // Хэрэв станцын харагдац бол станцуудын нийлбэр
            if ($isSystemView) {
                $systemTotal = ZConclusion::where('VAR', 'system_total_p')
                    ->whereBetween('TIMESTAMP_S', [$startTimestamp, $endTimestamp])
                    ->where('CALCULATION', 50)
                    ->orderByDesc('TIMESTAMP_S')
                    ->first();

                $totalP = $systemTotal ? (float)$systemTotal->VALUE : 0;
            } else {
                // Станцуудын нийлбэр
                $totalP = collect($typeSums)->sum('sumP');
            }

            return response()->json([
                'success' => true,
                'typeSums' => $typeSums,
                'totalP' => $totalP,
                'latestTimestamp' => $latestTimestamp,
                'isSystemView' => $isSystemView,
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

            $startDate = Carbon::parse($date)->startOfDay()->addHour();
            $endDate = Carbon::parse($date)->addDay()->startOfDay();

            $isSystemView = $this->getUserStationVars() === null;

            // Режим мэдээлэл
            $regimeValues = collect();

            if ($isSystemView) {
                // Системийн хэрэглэгч: Regime table-аас "Хэрэглээ" авна
                $regimeRecord = Regime::where('plant_name', 'Хэрэглээ')
                    ->whereDate('date', $date)
                    ->first();

                if ($regimeRecord) {
                    for ($i = 1; $i <= 24; $i++) {
                        $regimeValues->push($regimeRecord->{'t' . $i});
                    }
                }
            } else {
                // Станцын хэрэглэгч: ElectricDailyRegime table-аас өөрийн горимыг авна
                $mainPowerPlant = $this->getUserMainPowerPlant();

                if ($mainPowerPlant) {
                    $dailyRegime = ElectricDailyRegime::where('power_plant_id', $mainPowerPlant->id)
                        ->whereDate('date', $date)
                        ->first();

                    if ($dailyRegime) {
                        for ($i = 1; $i <= 24; $i++) {
                            $regimeValues->push($dailyRegime->{'hour_' . $i});
                        }
                    }
                }
            }

            // ZConclusion мэдээлэл
            if ($isSystemView) {
                // Системийн нийт
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
            } else {
                // Станцын нийлбэр
                $stationGroups = $this->filterStationGroups();
                $allStations = collect($stationGroups)->flatMap(function ($group) {
                    return $group['stations'];
                })->toArray();

                $zconclusionData = ZConclusion::select(
                    DB::raw('HOUR(FROM_UNIXTIME(timestamp_s)) as hour_num'),
                    DB::raw('FROM_UNIXTIME(timestamp_s, "%H:00") as hour'),
                    DB::raw('SUM(CAST(VALUE AS DECIMAL(10,2))) as value')
                )
                    ->whereIn('VAR', $allStations)
                    ->where('calculation', 50)
                    ->whereBetween('timestamp_s', [
                        $startDate->timestamp,
                        $endDate->timestamp
                    ])
                    ->groupBy('hour_num', 'hour')
                    ->orderBy('hour_num')
                    ->get();
            }

            // 24 цагийн timeline үүсгэх
            $allHours = collect();
            for ($i = 1; $i <= 24; $i++) {
                $hour = $i % 24;
                $allHours->push(str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00');
            }

            $zconclusionMap = $zconclusionData->mapWithKeys(function ($item) {
                return [$item->hour => (float)$item->value];
            });

            $zconclusionValues = $allHours->map(function ($hour) use ($zconclusionMap) {
                return $zconclusionMap->get($hour);
            });

            // Оргил ачаалал
            $peakLoad = [
                'time' => null,
                'value' => null,
                'source' => null
            ];

            if ($zconclusionValues->filter()->isNotEmpty()) {
                $zconclusionPeakValue = $zconclusionValues->max();
                $zconclusionPeakTime = $allHours[$zconclusionValues->search($zconclusionPeakValue)];

                $peakLoad = [
                    'time' => $zconclusionPeakTime,
                    'value' => $zconclusionPeakValue,
                    'source' => 'ZConclusion',
                    'formatted_value' => number_format($zconclusionPeakValue, 2)
                ];
            }

            return response()->json([
                'success' => true,
                'date' => $date,
                'times' => $allHours,
                'regimeValues' => $regimeValues->map(function ($value) {
                    return $value ? (float)$value : null;
                })->values(),
                'zconclusionValues' => $zconclusionValues,
                'peakLoad' => $peakLoad,
                'isSystemView' => $isSystemView,
                'regimeCount' => $regimeValues->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Холболтын алдаа гарлаа'
            ], 500);
        }
    }
}
