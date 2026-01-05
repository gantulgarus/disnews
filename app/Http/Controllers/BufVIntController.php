<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BufVInt;
use App\Models\RuFiderDaily;
use Illuminate\Http\Request;
use App\Models\BufFiderDaily;
use Illuminate\Support\Facades\DB;
use App\Services\RussianXmlImportService;

class BufVIntController extends Controller
{
    public function todayData(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        try {
            $carbonDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $carbonDate = Carbon::today();
        }

        // 1. Монголын өгөгдөл
        $rawPivot = BufFiderDaily::getPivotData($carbonDate);

        // pivot-ийн key-г Москвагийн цагт хөрвүүлж, огноог хадгалах
        $pivotTemp = [];
        $timeToDateMap = [];

        foreach ($rawPivot as $ubTime => $fidData) {
            $moscowDateTime = Carbon::createFromFormat('Y-m-d H:i', $carbonDate->toDateString() . ' ' . $ubTime)
                ->subHours(5);

            $moscowTime = $moscowDateTime->format('H:i');
            $moscowDate = $moscowDateTime->toDateString();

            $pivotTemp[$moscowTime] = [
                'ub_time' => $ubTime,
                'moscow_time' => $moscowTime,
                'moscow_date' => $moscowDate,
                'data' => $fidData,
            ];

            $timeToDateMap[$moscowTime] = $moscowDate;
        }

        // Москвагийн цагаар эрэмбэлэх
        ksort($pivotTemp);
        $pivot = $pivotTemp;

        // 2. Оросын өгөгдөл
        $yesterday = $carbonDate->copy()->subDay()->toDateString();
        $today = $carbonDate->toDateString();

        $russianDataRaw = RuFiderDaily::whereRaw('DATE(ognoo) IN (?, ?)', [$yesterday, $today])
            ->whereIn('fider', [257, 258, 110])
            ->select('ognoo', 'time_display', 'fider', 'import_kwt', 'export_kwt')
            ->get();

        // Оросын датаг зөв огноотой холбох
        $russianData = [];
        foreach ($pivot as $moscowTime => $timeData) {
            $requiredDate = $timeToDateMap[$moscowTime];

            foreach ([257, 258, 110] as $fider) {
                $rows = $russianDataRaw->filter(function ($item) use ($requiredDate, $moscowTime, $fider) {
                    $itemDate = Carbon::parse($item->ognoo)->format('Y-m-d');
                    $itemTime = substr($item->time_display, 0, 5);

                    if (strlen($itemTime) == 4 && strpos($itemTime, ':') == 1) {
                        $itemTime = '0' . $itemTime;
                    }

                    return $itemDate === $requiredDate
                        && $itemTime === $moscowTime
                        && $item->fider == $fider;
                });

                if (!isset($russianData[$moscowTime])) {
                    $russianData[$moscowTime] = [];
                }
                if (!isset($russianData[$moscowTime][$fider])) {
                    $russianData[$moscowTime][$fider] = [];
                }

                if ($rows->count() > 0) {
                    $totalImport = $rows->sum('import_kwt');
                    $totalExport = $rows->sum('export_kwt');

                    $russianData[$moscowTime][$fider][0] = (object)[
                        'import_kwt' => $totalImport,
                        'export_kwt' => $totalExport,
                    ];
                } else {
                    $russianData[$moscowTime][$fider][0] = (object)[
                        'import_kwt' => 0,
                        'export_kwt' => 0,
                    ];
                }
            }
        }

        // Debug мэдээлэл
        $debug = [
            'total_records' => BufFiderDaily::forDate($carbonDate)->count(),
            'fiders_in_db' => BufFiderDaily::forDate($carbonDate)->distinct('FIDER')->pluck('FIDER')->toArray(),
            'russian_records' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$today])
                ->whereIn('fider', [257, 258, 110])
                ->count(),
            'russian_yesterday_records' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$yesterday])
                ->whereIn('fider', [257, 258, 110])
                ->count(),
            'russian_times_today' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$today])
                ->whereIn('fider', [257, 258, 110])
                ->distinct()
                ->pluck('time_display')
                ->take(10)
                ->toArray(),
            'russian_times_yesterday' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$yesterday])
                ->whereIn('fider', [257, 258, 110])
                ->distinct()
                ->pluck('time_display')
                ->take(10)
                ->toArray(),
            'pivot_moscow_times' => array_slice(array_keys($pivot), 0, 10),
            'sample_russian_data' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$today])
                ->whereIn('fider', [257, 258, 110])
                ->limit(3)
                ->get()
                ->toArray(),
            'pivot_sample' => array_slice($pivot, 0, 3, true),
            'time_to_date_map_sample' => array_slice($timeToDateMap, 0, 10, true),
        ];

        return view('bufvint.today', compact(
            'pivot',
            'russianData',
            'carbonDate',
            'debug'
        ));
    }

    public function importRussianXml(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
        ]);

        $storagePath = storage_path('app/ru_xml');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $path = $request->file('xml_file')->store('ru_xml');
        $fullPath = storage_path('app/private/' . $path);

        if (!file_exists($fullPath)) {
            return back()->withErrors('Файл хадгалагдсангүй: ' . $fullPath);
        }

        RussianXmlImportService::import(
            $fullPath,
            [
                '032050025105102' => 257,
                '032050025105202' => 258,
                '032050025105901' => 110,
            ]
        );

        return back()->with('success', 'Орос талын XML амжилттай импортлогдлоо');
    }
}
