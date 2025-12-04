<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Tnews;
use App\Models\DisCoal;
use App\Models\PowerPlant;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use App\Models\StationThermoData;
use Illuminate\Support\Facades\DB;
use App\Models\DailyBalanceJournal;
use App\Models\PowerDistributionWork;
use App\Models\PowerPlantDailyReport;
use App\Models\WesternRegionCapacity;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }
    // Диспечерийн хоногийн мэдээ
    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Өмнөх өдөр
        $previousStart = Carbon::parse($date)->subDay()->startOfDay();
        $previousEnd   = Carbon::parse($date)->subDay()->endOfDay();

        $getData = function ($var, $calculation) use ($previousStart, $previousEnd) {
            try {
                return ZConclusion::selectRaw('MAX(CAST(value AS DECIMAL(10,2))) AS max_value, MIN(CAST(value AS DECIMAL(10,2))) AS min_value')
                    ->whereBetween(DB::raw('FROM_UNIXTIME(timestamp_s)'), [$previousStart, $previousEnd])
                    ->where('var', $var)
                    ->where('calculation', $calculation)
                    ->first();
            } catch (\Exception $e) {
                // Хэрвээ холболтын алдаа гарвал хоосон (null) утга буцаана
                return (object) [
                    'max_value' => null,
                    'min_value' => null,
                ];
            }
        };

        $system_data = $getData('system_total_p', '50');
        $import_data = $getData('import_total_p', '50');


        $journals = DailyBalanceJournal::select(
            DB::raw('DATE(date) as report_date'),
            DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
            DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
        )
            ->whereDate('date', $date)
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('report_date', 'desc')
            ->get();

        $monthStart = Carbon::parse($date)->startOfMonth();

        $monthToDate = DailyBalanceJournal::select(
            DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
            DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
        )
            ->whereBetween('date', [$monthStart, $date])
            ->first();


        $powerPlants = PowerPlant::with([
            // Тоноглол бүрийн хамгийн сүүлийн статус
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },

            // PowerInfo
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
            },
        ])
            ->where('power_plant_type_id', 1)
            ->where('region', 'ТБЭХС')
            ->orderBy('Order')
            ->get()
            ->map(function ($plant) {
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });


        // dd($powerPlants);

        $sunWindPlants = PowerPlant::with([
            // Тоноглол бүрийн хамгийн сүүлийн статус
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },

            // PowerInfo
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
            },
        ])->whereIn('power_plant_type_id', [2, 4])->where('region', 'ТБЭХС')->orderBy('Order')->get()
            ->map(function ($plant) {
                // powerInfos дотроос P болон Pmax талбарууд байгаа гэж үзье
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        $battery_powers = PowerPlant::with([
            // Тоноглол бүрийн хамгийн сүүлийн статус
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },

            // PowerInfo
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
            },
        ])->where('power_plant_type_id', 3)->where('region', 'ТБЭХС')->orderBy('Order')->get()
            ->map(function ($plant) {
                // powerInfos дотроос P болон Pmax талбарууд байгаа гэж үзье
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        $tasralts = Tnews::whereDate('date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $power_distribution_works = PowerDistributionWork::whereDate('date', $date)
            ->with('user')
            ->get();

        // 6:00 цагийн мэдээг авах
        $station_thermo_data = StationThermoData::where('infodate', $date)
            ->where('infotime', '06:00:00')
            ->first();

        // ✅ Хэрвээ нийт дүн хэрэгтэй бол
        $total_p = $powerPlants->sum('total_p');
        $total_pmax = $powerPlants->sum('total_pmax');
        $sun_wind_total_p = $sunWindPlants->sum('total_p');
        $sun_wind_total_pmax = $sunWindPlants->sum('total_pmax');
        $battery_total_p = $battery_powers->sum('total_p');
        $battery_total_pmax = $battery_powers->sum('total_pmax');

        // Түлшний мэдээ
        $disCoals = DisCoal::whereDate('date', $date)
            ->orderBy('created_at', 'desc')
            ->get();


        return view('reports.daily_report', compact('date', 'system_data', 'import_data', 'journals', 'monthToDate', 'powerPlants', 'tasralts', 'power_distribution_works', 'station_thermo_data', 'total_p', 'total_pmax', 'disCoals', 'sunWindPlants', 'sun_wind_total_p', 'sun_wind_total_pmax', 'battery_powers', 'battery_total_p', 'battery_total_pmax'));
    }

    // Орон нутгийн хоногийн мэдээ
    public function localDailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $powerPlants = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },

            // PowerInfo
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
            },

        ])->where('region', 'ББЭХС')->orderBy('Order')->get()
            ->map(function ($plant) {
                // powerInfos дотроос P болон Pmax талбарууд байгаа гэж үзье
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        // ✅ Хэрвээ нийт дүн хэрэгтэй бол
        $bbehs_total_p = $powerPlants->sum('total_p');
        $bbehs_total_pmax = $powerPlants->sum('total_pmax');

        $powerAltaiPlants = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },

            // PowerInfo
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
            },
        ])->where('region', 'АУЭХС')->orderBy('Order')->get()
            ->map(function ($plant) {
                // powerInfos дотроос P болон Pmax талбарууд байгаа гэж үзье
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        // ✅ Хэрвээ нийт дүн хэрэгтэй бол
        $altai_total_p = $powerAltaiPlants->sum('total_p');
        $altai_total_pmax = $powerAltaiPlants->sum('total_pmax');

        $westernRegionCapacities = WesternRegionCapacity::whereDate('date', $date)->get();

        return view('reports.local_daily_report', compact('powerPlants', 'date', 'bbehs_total_p', 'bbehs_total_pmax', 'powerAltaiPlants', 'altai_total_p', 'altai_total_pmax', 'westernRegionCapacities'));
    }

    public function powerPlantReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Set time range from 01:00:00 of selected date to 00:00:00 of next day
        $startDate = Carbon::parse($date)->startOfDay()->addHour(); // 01:00:00
        $endDate = Carbon::parse($date)->addDay()->startOfDay();    // 00:00:00 next day

        $vars = [
            'SALKHIT_WPP_TOTAL_P',
            'TSETSII_WPP_TOTAL_P',
            'SHAND_WPP_TOTAL_P',
            'DARKHAN_SPP_TOTAL_P',
            'MONNAR_SPP_TOTAL_P',
            'GEGEEN_SPP_TOTAL_P',
            'SUMBER_SPP_TOTAL_P',
            'BUHUG_SPP_TOTAL_P',
            'GOVI_SPP_TOTAL_P',
            'ERDENE_SPP_TOTAL_P',
        ];

        $results = ZConclusion::select(
            DB::raw('FROM_UNIXTIME(TIMESTAMP_S) as date'),
            'VAR',
            'VALUE'
        )
            ->whereBetween(DB::raw('FROM_UNIXTIME(TIMESTAMP_S)'), [$startDate, $endDate])
            ->where('CALCULATION', 50)
            ->whereIn('VAR', $vars)
            ->orderBy('TIMESTAMP_S')
            ->get();

        return view('reports.power_plant_report', compact('date', 'results'));
    }
}