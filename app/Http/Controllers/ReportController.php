<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Tnews;
use App\Models\PowerPlant;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use App\Models\StationThermoData;
use Illuminate\Support\Facades\DB;
use App\Models\DailyBalanceJournal;
use App\Models\PowerDistributionWork;
use App\Models\PowerPlantDailyReport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }
    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $journals = DailyBalanceJournal::select(
            DB::raw('DATE(entry_date_time) as report_date'),
            DB::raw('COALESCE(SUM(processed_amount), 0) as total_processed'),
            DB::raw('COALESCE(SUM(distribution_amount), 0) as total_distribution')
        )
            ->whereDate('entry_date_time', $date)
            ->groupBy(DB::raw('DATE(entry_date_time)'))
            ->orderBy('report_date', 'desc')
            ->get();


        // $powerPlantDailyReports = PowerPlantDailyReport::whereDate('report_date', $date)
        //     ->with('powerPlant')
        //     ->get();

        $powerPlants = PowerPlant::with([
            'equipmentStatuses' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            },
            'powerInfos' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }
        ])->get();

        $tasralts = Tnews::all();

        $power_distribution_works = PowerDistributionWork::whereDate('date', $date)
            ->with('user')
            ->get();

        // 6:00 цагийн мэдээг авах
        $station_thermo_data = StationThermoData::where('infodate', $date)
            ->where('infotime', '06:00:00')
            ->first();


        return view('reports.daily_report', compact('date', 'journals', 'powerPlants', 'tasralts', 'power_distribution_works', 'station_thermo_data'));
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
