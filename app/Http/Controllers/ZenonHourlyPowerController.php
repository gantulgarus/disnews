<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ZenonHourlyPowerController extends Controller
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

    // Станцуудын нэрийг монгол хэл дээр
    private $stationNames = [
        'PP4_TOTAL_P' => 'ДЦС-4',
        'PP3_TOTAL_P' => 'ДЦС-3',
        'PP2_TOTAL_P' => 'ДЦС-2',
        'DARKHAN_PP_TOTAL_P' => 'Дархан ДЦС',
        'ERDENET_PP_TOTAL_P' => 'Эрдэнэт ДЦС',
        'GOK_PP_TOTAL_P' => 'ГОК ДЦС',
        'DALANZADGAD_PP_TOTAL_P' => 'Даланзадгад ДЦС',
        'UHAAHUDAG_PP_TOTAL_P' => 'Ухаа Худаг ДЦС',
        'BUURULJUUT_TOTAL_P' => 'Бөөрөлжүүт ДЦС',
        'SALKHIT_WPP_TOTAL_P' => 'Салхит СЦС',
        'TSETSII_WPP_TOTAL_P' => 'Цэцүү СЦС',
        'SHAND_WPP_TOTAL_P' => 'Шанд СЦС',
        'DARKHAN_SPP_TOTAL_P' => 'Дархан НЦС',
        'MONNAR_SPP_TOTAL_P' => 'Моннар НЦС',
        'GEGEEN_SPP_TOTAL_P' => 'Гэгээн НЦС',
        'SUMBER_SPP_TOTAL_P' => 'Сүмбэр НЦС',
        'BUHUG_SPP_TOTAL_P' => 'Бөхөг НЦС',
        'GOVI_SPP_TOTAL_P' => 'Говь НЦС',
        'ERDENE_SPP_TOTAL_P' => 'Эрдэнэ НЦС',
        'ERDENE_SPP_BHB_TOTAL_P' => 'Эрдэнэ БХ',
        'BAGANUUR_BESS_TOTAL_P_T' => 'Багануур БХ',
        'SONGINO_BESS_TOTAL_P' => 'Сонгино БХ',
        'IMPORT_EXPORT_TOTAL_P' => 'Импорт',
    ];

    /**
     * Хэрэглэгчийн байгууллагын станцуудын VAR кодуудыг авах
     */
    private function getUserStationVars()
    {
        $user = Auth::user();

        if (!$user->organization_id) {
            return null;
        }

        $powerPlants = $user->organization->powerPlants;

        if ($powerPlants->isEmpty()) {
            return null;
        }

        return $powerPlants->pluck('short_name')->toArray();
    }

    /**
     * Хэрэглэгчийн эрхээр станцуудыг шүүх
     */
    private function filterStationGroups()
    {
        $userStationVars = $this->getUserStationVars();

        if ($userStationVars === null) {
            return $this->stationGroups;
        }

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
     * Цагийн мэдээлэл харуулах хуудас
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $stationGroups = $this->filterStationGroups();
        $isSystemView = $this->getUserStationVars() === null;

        // Станцуудын цаг тутмын мэдээллийг авах
        $hourlyData = $this->getHourlyData($date);

        return view('zenon.hourly-power', compact('date', 'stationGroups', 'isSystemView', 'hourlyData'));
    }

    /**
     * Станцуудын цаг тутмын чадлын мэдээллийг авах
     */
    private function getHourlyData($date)
    {
        try {
            $startDate = Carbon::parse($date)->startOfDay()->addHour();
            $endDate = Carbon::parse($date)->addDay()->startOfDay();

            $stationGroups = $this->filterStationGroups();
            $allStations = collect($stationGroups)->flatMap(function ($group) {
                return $group['stations'];
            })->toArray();

            if (empty($allStations)) {
                return null;
            }

            // 24 цагийн мэдээллийг авах
            $hourlyData = ZConclusion::select(
                'VAR',
                DB::raw('HOUR(FROM_UNIXTIME(timestamp_s)) as hour_num'),
                DB::raw('AVG(CAST(VALUE AS DECIMAL(10,2))) as avg_value')
            )
                ->whereIn('VAR', $allStations)
                ->where('calculation', 50)
                ->whereBetween('timestamp_s', [
                    $startDate->timestamp,
                    $endDate->timestamp
                ])
                ->groupBy('VAR', 'hour_num')
                ->orderBy('VAR')
                ->orderBy('hour_num')
                ->get();

            // Мэдээллийг бүлгээр бүтцжүүлэх
            $structuredData = [];

            foreach ($stationGroups as $groupKey => $group) {
                $stations = [];

                foreach ($group['stations'] as $stationVar) {
                    $stationHourlyData = [];

                    // 24 цагийн мэдээлэл бэлдэх
                    for ($hour = 1; $hour <= 24; $hour++) {
                        $hourIndex = $hour % 24;

                        $value = $hourlyData
                            ->where('VAR', $stationVar)
                            ->where('hour_num', $hourIndex)
                            ->first();

                        $stationHourlyData[] = $value ? round($value->avg_value, 2) : null;
                    }

                    $stations[] = [
                        'var' => $stationVar,
                        'name' => $this->stationNames[$stationVar] ?? $stationVar,
                        'hourly_data' => $stationHourlyData,
                        'total' => round(array_sum(array_filter($stationHourlyData)), 2),
                        'average' => round(
                            count(array_filter($stationHourlyData)) > 0
                                ? array_sum(array_filter($stationHourlyData)) / count(array_filter($stationHourlyData))
                                : 0,
                            2
                        ),
                    ];
                }

                $structuredData[$groupKey] = [
                    'name' => $group['name'],
                    'stations' => $stations
                ];
            }

            return $structuredData;
        } catch (\Exception $e) {
            Log::error('Hourly power data error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 19:00 цагийн ачаалал
     */
    public function evening(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $stationGroups = $this->filterStationGroups();

        // 19:00 цагийн мэдээллийг авах
        $eveningData = $this->getEveningData($date);

        // Статистик тооцоолох
        $statistics = $this->calculateStatistics($eveningData);

        return view('zenon.evening-power', array_merge(
            compact('date', 'eveningData'),
            $statistics
        ));
    }

    /**
     * 19:00 цагийн мэдээлэл авах
     */
    private function getEveningData($date)
    {
        try {
            $targetTime = Carbon::parse($date)->setTime(19, 0, 0);
            $stationGroups = $this->filterStationGroups();

            $allStations = collect($stationGroups)->flatMap(function ($group) {
                return $group['stations'];
            })->toArray();

            if (empty($allStations)) {
                return null;
            }

            // Станцуудын мэдээлэл болон SYSTEM_TOTAL_P авах
            $allVars = array_merge($allStations, ['SYSTEM_TOTAL_P']);

            // 19:00 цагийн мэдээллийг авах
            $data = ZConclusion::select('VAR', DB::raw('AVG(CAST(VALUE AS DECIMAL(10,2))) as avg_value'))
                ->whereIn('VAR', $allVars)
                ->where('calculation', 50)
                ->whereBetween('timestamp_s', [
                    $targetTime->timestamp,
                    $targetTime->addMinutes(59)->timestamp
                ])
                ->groupBy('VAR')
                ->get();

            // Бүтцжүүлэх
            $structuredData = [];
            foreach ($stationGroups as $groupKey => $group) {
                $stations = [];
                foreach ($group['stations'] as $stationVar) {
                    $value = $data->where('VAR', $stationVar)->first();
                    $stations[] = [
                        'var' => $stationVar,
                        'name' => $this->stationNames[$stationVar] ?? $stationVar,
                        'value' => $value ? round($value->avg_value, 2) : 0,
                    ];
                }

                $structuredData[$groupKey] = [
                    'name' => $group['name'],
                    'stations' => $stations
                ];
            }

            return $structuredData;
        } catch (\Exception $e) {
            Log::error('Evening power data error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Статистик тооцоолох
     */
    private function calculateStatistics($eveningData)
    {
        if ($eveningData === null) {
            return [
                'systemTotal' => 0,
                'totalLoad' => 0,
                'activeStations' => 0,
                'totalStations' => 0,
                'averageLoad' => 0,
                'maxLoad' => 0,
                'maxStationName' => '-'
            ];
        }

        $totalLoad = 0;
        $activeStations = 0;
        $totalStations = 0;
        $maxLoad = 0;
        $maxStationName = '-';

        foreach ($eveningData as $groupData) {
            foreach ($groupData['stations'] as $station) {
                $totalStations++;
                $totalLoad += $station['value'];

                if ($station['value'] > 0) {
                    $activeStations++;
                }

                if ($station['value'] > $maxLoad) {
                    $maxLoad = $station['value'];
                    $maxStationName = $station['name'];
                }
            }
        }

        return [
            'systemTotal' => $this->getSystemTotal($eveningData),
            'totalLoad' => $totalLoad,
            'activeStations' => $activeStations,
            'totalStations' => $totalStations,
            'averageLoad' => $totalStations > 0 ? $totalLoad / $totalStations : 0,
            'maxLoad' => $maxLoad,
            'maxStationName' => $maxStationName
        ];
    }

    /**
     * Системийн нийт ачааллыг авах
     */
    private function getSystemTotal($eveningData)
    {
        try {
            $date = request()->input('date', now()->format('Y-m-d'));
            $targetTime = Carbon::parse($date)->setTime(19, 0, 0);

            $systemData = ZConclusion::select(DB::raw('AVG(CAST(VALUE AS DECIMAL(10,2))) as avg_value'))
                ->where('VAR', 'SYSTEM_TOTAL_P')
                ->where('calculation', 50)
                ->whereBetween('timestamp_s', [
                    $targetTime->timestamp,
                    $targetTime->addMinutes(59)->timestamp
                ])
                ->first();

            return $systemData ? round($systemData->avg_value, 2) : 0;
        } catch (\Exception $e) {
            Log::error('System total error: ' . $e->getMessage());
            return 0;
        }
    }
}
