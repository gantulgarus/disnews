<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Tnews;
use App\Models\Regime;
use App\Models\DisCoal;
use App\Models\PowerPlant;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use App\Models\StationThermoData;
use Illuminate\Support\Facades\DB;
use App\Models\AltaiRegionCapacity;
use App\Models\DailyBalanceJournal;
use App\Models\PowerDistributionWork;
use App\Models\PowerPlantDailyReport;
use App\Models\WesternRegionCapacity;
use App\Models\DailyBalanceImportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }
    // Диспечерийн хоногийн мэдээ
    // public function dailyReport(Request $request)
    // {
    //     $date = $request->input('date', now()->toDateString());

    //     // Өмнөх өдөр
    //     $previousStart = Carbon::parse($date)->subDay()->startOfDay();
    //     $previousEnd   = Carbon::parse($date)->subDay()->endOfDay();

    //     $getData = function ($var, $calculation) use ($previousStart, $previousEnd) {
    //         try {
    //             return ZConclusion::selectRaw('MAX(CAST(value AS DECIMAL(10,2))) AS max_value, MIN(CAST(value AS DECIMAL(10,2))) AS min_value')
    //                 ->whereBetween(DB::raw('FROM_UNIXTIME(timestamp_s)'), [$previousStart, $previousEnd])
    //                 ->where('var', $var)
    //                 ->where('calculation', $calculation)
    //                 ->first();
    //         } catch (\Exception $e) {
    //             // Хэрвээ холболтын алдаа гарвал хоосон (null) утга буцаана
    //             return (object) [
    //                 'max_value' => null,
    //                 'min_value' => null,
    //             ];
    //         }
    //     };

    //     $system_data = $getData('system_total_p', '50');
    //     $import_data = $getData('import_total_p', '50');


    //     $journals = DailyBalanceJournal::select(
    //         DB::raw('DATE(date) as report_date'),
    //         DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
    //         DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
    //     )
    //         ->whereDate('date', $date)
    //         ->groupBy(DB::raw('DATE(date)'))
    //         ->orderBy('report_date', 'desc')
    //         ->get();

    //     $monthStart = Carbon::parse($date)->startOfMonth();

    //     $monthToDate = DailyBalanceJournal::select(
    //         DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
    //         DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
    //     )
    //         ->whereBetween('date', [$monthStart, $date])
    //         ->first();


    //     $powerPlants = PowerPlant::with([
    //         // Тоноглол бүрийн хамгийн сүүлийн статус
    //         'equipmentStatuses' => function ($q) use ($date) {
    //             $q->whereDate('date', $date)
    //                 ->whereIn('id', function ($sub) use ($date) {
    //                     $sub->selectRaw('MAX(id)')
    //                         ->from('equipment_statuses')
    //                         ->whereDate('date', $date)
    //                         ->groupBy('equipment_id');
    //                 });
    //         },

    //         // PowerInfo
    //         'powerInfos' => function ($q) use ($date) {
    //             $q->whereDate('date', $date)
    //                 ->orderByDesc('id')
    //                 ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
    //         },
    //     ])
    //         ->where('power_plant_type_id', 1)
    //         ->where('region', 'ТБЭХС')
    //         ->orderBy('Order')
    //         ->get()
    //         ->map(function ($plant) {
    //             $plant->total_p = $plant->powerInfos->sum('p');
    //             $plant->total_pmax = $plant->powerInfos->sum('p_max');
    //             return $plant;
    //         });


    //     // dd($powerPlants);

    //     $sunWindPlants = PowerPlant::with([
    //         // Тоноглол бүрийн хамгийн сүүлийн статус
    //         'equipmentStatuses' => function ($q) use ($date) {
    //             $q->whereDate('date', $date)
    //                 ->whereIn('id', function ($sub) use ($date) {
    //                     $sub->selectRaw('MAX(id)')
    //                         ->from('equipment_statuses')
    //                         ->whereDate('date', $date)
    //                         ->groupBy('equipment_id');
    //                 });
    //         },

    //         // PowerInfo
    //         'powerInfos' => function ($q) use ($date) {
    //             $q->whereDate('date', $date)
    //                 ->orderByDesc('id')
    //                 ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
    //         },
    //     ])->whereIn('power_plant_type_id', [2, 4])->where('region', 'ТБЭХС')->orderBy('Order')->get()
    //         ->map(function ($plant) {
    //             // powerInfos дотроос P болон Pmax талбарууд байгаа гэж үзье
    //             $plant->total_p = $plant->powerInfos->sum('p');
    //             $plant->total_pmax = $plant->powerInfos->sum('p_max');
    //             return $plant;
    //         });

    //     $battery_powers = PowerPlant::with([
    //         // Тоноглол бүрийн хамгийн сүүлийн статус
    //         'equipmentStatuses' => function ($q) use ($date) {
    //             $q->whereDate('date', $date)
    //                 ->whereIn('id', function ($sub) use ($date) {
    //                     $sub->selectRaw('MAX(id)')
    //                         ->from('equipment_statuses')
    //                         ->whereDate('date', $date)
    //                         ->groupBy('equipment_id');
    //                 });
    //         },

    //         // PowerInfo
    //         'powerInfos' => function ($q) use ($date) {
    //             $q->whereDate('date', $date)
    //                 ->orderByDesc('id')
    //                 ->limit(1); // ✅ Зөвхөн хамгийн сүүлийн бичлэг
    //         },
    //     ])->where('power_plant_type_id', 3)->where('region', 'ТБЭХС')->orderBy('Order')->get()
    //         ->map(function ($plant) {
    //             // powerInfos дотроос P болон Pmax талбарууд байгаа гэж үзье
    //             $plant->total_p = $plant->powerInfos->sum('p');
    //             $plant->total_pmax = $plant->powerInfos->sum('p_max');
    //             return $plant;
    //         });

    //     $tasralts = Tnews::whereDate('date', $date)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     $power_distribution_works = PowerDistributionWork::whereDate('date', $date)
    //         ->with('user')
    //         ->get();

    //     // 6:00 цагийн мэдээг авах
    //     $station_thermo_data = StationThermoData::where('infodate', $date)
    //         ->where('infotime', '06:00:00')
    //         ->first();

    //     // ✅ Хэрвээ нийт дүн хэрэгтэй бол
    //     $total_p = $powerPlants->sum('total_p');
    //     $total_pmax = $powerPlants->sum('total_pmax');
    //     $sun_wind_total_p = $sunWindPlants->sum('total_p');
    //     $sun_wind_total_pmax = $sunWindPlants->sum('total_pmax');
    //     $battery_total_p = $battery_powers->sum('total_p');
    //     $battery_total_pmax = $battery_powers->sum('total_pmax');

    //     // Түлшний мэдээ
    //     $disCoals = DisCoal::whereDate('date', $date)
    //         ->orderBy('created_at', 'desc')
    //         ->get();


    //     return view('reports.daily_report', compact('date', 'system_data', 'import_data', 'journals', 'monthToDate', 'powerPlants', 'tasralts', 'power_distribution_works', 'station_thermo_data', 'total_p', 'total_pmax', 'disCoals', 'sunWindPlants', 'sun_wind_total_p', 'sun_wind_total_pmax', 'battery_powers', 'battery_total_p', 'battery_total_pmax'));
    // }
    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // ========================================
        // 1. OPTIMIZED: ZConclusion min/max data
        // ========================================
        $previousStart = Carbon::parse($date)->subDay()->startOfDay()->timestamp;
        $previousEnd   = Carbon::parse($date)->subDay()->endOfDay()->timestamp;

        // Single query for both variables
        $stats = ZConclusion::selectRaw('
            VAR,
            MAX(CAST(VALUE AS DECIMAL(10,2))) AS max_value,
            MIN(CAST(VALUE AS DECIMAL(10,2))) AS min_value
        ')
            ->whereIn('VAR', ['SYSTEM_TOTAL_P', 'IMPORT_TOTAL_P'])
            ->where('CALCULATION', 50)
            ->whereBetween('TIMESTAMP_S', [$previousStart, $previousEnd])
            ->groupBy('VAR')
            ->get()
            ->keyBy('VAR');

        // ✅ Том үсгээр авах
        $system_data = $stats->get('SYSTEM_TOTAL_P', (object)[
            'max_value' => null,
            'min_value' => null,
        ]);

        $import_data = $stats->get('IMPORT_TOTAL_P', (object)[
            'max_value' => null,
            'min_value' => null,
        ]);

        // ========================================
        // 2. Daily Balance Journals
        // ========================================
        $journals = DailyBalanceJournal::select(
            DB::raw('DATE(date) as report_date'),
            DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
            DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
        )
            ->whereDate('date', $date)
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('report_date', 'desc')
            ->get();

        // ========================================
        // 3. Month to Date
        // ========================================
        $monthStart = Carbon::parse($date)->startOfMonth();

        $monthToDate = DailyBalanceJournal::select(
            DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
            DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
        )
            ->whereBetween('date', [$monthStart, $date])
            ->first();

        // ========================================
        // 4. Import/Export - Daily (тухайн өдөр)
        // ========================================
        $dailyImportExport = DailyBalanceImportExport::select(
            DB::raw('DATE(date) as report_date'),
            DB::raw('COALESCE(SUM(import), 0) as total_import'),
            DB::raw('COALESCE(SUM(export), 0) as total_export'),
            DB::raw('COALESCE(SUM(import) - SUM(export), 0) as net_import') // Цэвэр импорт
        )
            ->whereDate('date', $date)
            ->groupBy(DB::raw('DATE(date)'))
            ->first();

        // ========================================
        // 5. Import/Export - Month to Date (сарын нийлбэр)
        // ========================================
        $monthToDateImportExport = DailyBalanceImportExport::select(
            DB::raw('COALESCE(SUM(import), 0) as total_import'),
            DB::raw('COALESCE(SUM(export), 0) as total_export'),
            DB::raw('COALESCE(SUM(import) - SUM(export), 0) as net_import')
        )
            ->whereBetween('date', [$monthStart, $date])
            ->first();

        // ========================================
        // 4. OPTIMIZED: Power Plants with Latest Status
        // ========================================

        // Get latest equipment status IDs for the date (single query)
        $latestEquipmentStatusIds = DB::table('equipment_statuses')
            ->select('equipment_id', DB::raw('MAX(id) as latest_id'))
            ->whereDate('date', $date)
            ->groupBy('equipment_id')
            ->pluck('latest_id');

        // Get latest power info IDs for the date (single query)
        $latestPowerInfoIds = DB::table('station_power_infos')
            ->select('power_plant_id', DB::raw('MAX(id) as latest_id'))
            ->whereDate('date', $date)
            ->groupBy('power_plant_id')
            ->pluck('latest_id');

        // Load thermal power plants with optimized relationships
        $powerPlants = PowerPlant::forDailyReport()->with([
            'equipmentStatuses' => function ($q) use ($latestEquipmentStatusIds) {
                $q->whereIn('id', $latestEquipmentStatusIds);
            },
            'powerInfos' => function ($q) use ($latestPowerInfoIds) {
                $q->whereIn('id', $latestPowerInfoIds);
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

        // ========================================
        // 5. OPTIMIZED: Sun & Wind Plants
        // ========================================
        $sunWindPlants = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($latestEquipmentStatusIds) {
                $q->whereIn('id', $latestEquipmentStatusIds);
            },
            'powerInfos' => function ($q) use ($latestPowerInfoIds) {
                $q->whereIn('id', $latestPowerInfoIds);
            },
        ])
            ->whereIn('power_plant_type_id', [2, 4])
            ->where('region', 'ТБЭХС')
            ->orderBy('Order')
            ->get()
            ->map(function ($plant) {
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        // ========================================
        // 6. OPTIMIZED: Battery Powers
        // ========================================
        $battery_powers = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($latestEquipmentStatusIds) {
                $q->whereIn('id', $latestEquipmentStatusIds);
            },
            'powerInfos' => function ($q) use ($latestPowerInfoIds) {
                $q->whereIn('id', $latestPowerInfoIds);
            },
        ])
            ->where('power_plant_type_id', 3)
            ->where('region', 'ТБЭХС')
            ->orderBy('Order')
            ->get()
            ->map(function ($plant) {
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        // ========================================
        // 7. Other Data
        // ========================================
        $tasralts = Tnews::whereDate('date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $power_distribution_works = PowerDistributionWork::whereDate('date', $date)
            ->with('user')
            ->get();

        $station_thermo_data = StationThermoData::where('infodate', $date)
            ->where('infotime', '06:00:00')
            ->first();

        // ========================================
        // 8. Calculate Totals
        // ========================================
        $total_p = $powerPlants->sum('total_p');
        $total_pmax = $powerPlants->sum('total_pmax');
        $sun_wind_total_p = $sunWindPlants->sum('total_p');
        $sun_wind_total_pmax = $sunWindPlants->sum('total_pmax');
        $battery_total_p = $battery_powers->sum('total_p');
        $battery_total_pmax = $battery_powers->sum('total_pmax');

        // ========================================
        // 9. Fuel Information
        // ========================================
        $disCoals = DisCoal::whereDate('date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.daily_report', compact(
            'date',
            'system_data',
            'import_data',
            'journals',
            'monthToDate',
            'powerPlants',
            'tasralts',
            'power_distribution_works',
            'station_thermo_data',
            'total_p',
            'total_pmax',
            'disCoals',
            'sunWindPlants',
            'sun_wind_total_p',
            'sun_wind_total_pmax',
            'battery_powers',
            'battery_total_p',
            'battery_total_pmax',
            'dailyImportExport',
            'monthToDateImportExport'
        ));
    }

    // Орон нутгийн хоногийн мэдээ
    public function localDailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // ====================================
        // ББЭХС - Western Region
        // ====================================
        $powerPlants = PowerPlant::with([
            'equipments',
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1);
            },
            'powerPlantType'
        ])
            ->where('region', 'ББЭХС')
            ->orderBy('Order')
            ->get()
            ->map(function ($plant) {
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        // ББЭХС нийт дүн
        $bbehs_total_p = $powerPlants->sum('total_p');
        $bbehs_total_pmax = $powerPlants->sum('total_pmax');

        // ББЭХС станцуудын нийлбэр (Хэрэглээ, Түгээлт тооцоолоход хэрэгтэй)
        $bbehs_total_produced = $powerPlants->sum(function ($plant) {
            return $plant->powerInfos->first()?->produced_energy ?? 0;
        });

        $bbehs_total_distributed = $powerPlants->sum(function ($plant) {
            return $plant->powerInfos->first()?->distributed_energy ?? 0;
        });

        // ББЭХС системийн мэдээлэл
        $westernRegionCapacities = WesternRegionCapacity::whereDate('date', $date)->get();

        // Импортын түгээсэн ЦЭХ
        $import_distributed = $westernRegionCapacities->first()?->import_distributed ?? 0;

        // ✅ Хэрэглээ = Станцуудын үйлдвэрлэсэн + Импортын түгээсэн
        $bbehs_consumption = $bbehs_total_produced + $import_distributed;

        // ✅ Түгээлт = Станцуудын түгээсэн + Импортын түгээсэн
        $bbehs_distribution = $bbehs_total_distributed + $import_distributed;

        // ====================================
        // АУЭХС - Altai Region
        // ====================================
        $powerAltaiPlants = PowerPlant::with([
            'equipments',
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->whereIn('id', function ($sub) use ($date) {
                        $sub->selectRaw('MAX(id)')
                            ->from('equipment_statuses')
                            ->whereDate('date', $date)
                            ->groupBy('equipment_id');
                    });
            },
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date)
                    ->orderByDesc('id')
                    ->limit(1);
            },
            'powerPlantType'
        ])
            ->where('region', 'АУЭХС')
            ->orderBy('Order')
            ->get()
            ->map(function ($plant) {
                $plant->total_p = $plant->powerInfos->sum('p');
                $plant->total_pmax = $plant->powerInfos->sum('p_max');
                return $plant;
            });

        // АУЭХС нийт дүн
        $altai_total_p = $powerAltaiPlants->sum('total_p');
        $altai_total_pmax = $powerAltaiPlants->sum('total_pmax');

        // ББЭХС станцуудын нийлбэр (Хэрэглээ, Түгээлт тооцоолоход хэрэгтэй)
        $altai_total_produced = $powerAltaiPlants->sum(function ($plant) {
            return $plant->powerInfos->first()?->produced_energy ?? 0;
        });

        $altai_total_distributed = $powerAltaiPlants->sum(function ($plant) {
            return $plant->powerInfos->first()?->distributed_energy ?? 0;
        });

        // АУЭХС системийн мэдээлэл
        $altaiRegionCapacities = AltaiRegionCapacity::whereDate('date', $date)->get();

        return view('reports.local_daily_report', compact(
            'date',
            'powerPlants',
            'bbehs_total_p',
            'bbehs_total_pmax',
            'bbehs_total_produced',
            'bbehs_total_distributed',
            'bbehs_consumption',
            'bbehs_distribution',
            'westernRegionCapacities',
            'powerAltaiPlants',
            'altai_total_p',
            'altai_total_pmax',
            'altai_total_produced',
            'altai_total_distributed',
            'altaiRegionCapacities'
        ));
    }

    // СЭХ станцуудын горим, гүйцэтгэл
    public function powerPlantRenewableReport(Request $request)
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
            'ERDENE_SPP_BHB_TOTAL_P',
            'BAGANUUR_BESS_TOTAL_P_T',
            'SONGINO_BESS_TOTAL_P'
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


        // Regime хүснэгтийн plant_name жагсаалт
        $plantNames = [
            'Салхит СЦС',
            'Цэций СЦС',
            'Шанд СЦС',
            'Дархан НЦС',
            'Моннаран НЦС',
            'Гэгээн НЦС',
            'Сүмбэр НЦС',
            'Бөхөг НЦС',
            'Говь НЦС',
            'Эрдэнэ НЦС',
            'Эрдэнэ НЦС БХС',
            'Багануур БХС',
            'Сонгино БХС',
        ];

        // Regime хүснэгтээс тухайн өдрийн горим авах
        $regimeData = Regime::whereDate('date', $date)
            ->whereIn('plant_name', $plantNames)
            ->orderBy('plant_name')
            ->get();

        return view('reports.power_plant_renewable_report', compact('date', 'results', 'regimeData'));
    }

    // ДЦС-ын горим, гүйцэтгэлийн тайлан
    public function powerPlantReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Set time range from 01:00:00 of selected date to 00:00:00 of next day
        $startDate = Carbon::parse($date)->startOfDay()->addHour(); // 01:00:00
        $endDate = Carbon::parse($date)->addDay()->startOfDay();    // 00:00:00 next day

        $vars = [
            'PP4_TOTAL_P',
            'PP3_TOTAL_P',
            'PP2_TOTAL_P',
            'DARKHAN_PP_TOTAL_P',
            'ERDENET_PP_TOTAL_P',
            'GOK_PP_TOTAL_P',
            'DALANZADGAD_PP_TOTAL_P',
            'UHAAHUDAG_PP_TOTAL_P',
            'BUURULJUUT_PP_TOTAL_P',
            'TOSON_PP_TOTAL_P',
            'IMPORT_TOTAL_P',
            'EXPORT_TOTAL_P',
            'SYSTEM_TOTAL_P',
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


        // Regime хүснэгтийн plant_name жагсаалт
        $plantNames = [
            'ДЦС-4',
            'ДЦС-3',
            'ДЦС-2',
            'ДДЦС',
            'ЭДЦС',
            'ЭҮ-н ДЦС',
            'Даланзадгад ДЦС',
            'УХГ ЦС',
            'Тосон ДЦС',
            'Импорт/экспорт',
            'Хэрэглээ'
        ];

        // Regime хүснэгтээс тухайн өдрийн горим авах
        $regimeData = Regime::whereDate('date', $date)
            ->whereIn('plant_name', $plantNames)
            ->orderBy('plant_name')
            ->get();

        return view('reports.power_plant_report', compact('date', 'results', 'regimeData'));
    }
}