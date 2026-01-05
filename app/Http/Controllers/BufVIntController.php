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
    // public function todayData(Request $request)
    // {
    //     $date = $request->input('date', Carbon::today()->toDateString());

    //     try {
    //         $moscowDate = Carbon::parse($date); // Энэ нь МОСКВАГИЙН огноо
    //     } catch (\Exception $e) {
    //         $moscowDate = Carbon::today();
    //     }

    //     // Москвагийн тухайн өдрийн 00:00-23:30 харуулах
    //     // Үүнд:
    //     // - УБ өмнөх өдрийн 05:00-23:30 (Москва 00:00-18:30)
    //     // - УБ тухайн өдрийн 00:00-04:30 (Москва 19:00-23:30)

    //     $prevDayUB = $moscowDate->copy()->subDay(); // УБ өмнөх өдөр
    //     $todayUB = $moscowDate->copy(); // УБ тухайн өдөр

    //     // 1. Монголын өгөгдөл - хоёр өдрийн дата
    //     $rawPivotPrev = BufFiderDaily::getPivotData($prevDayUB);
    //     $rawPivotToday = BufFiderDaily::getPivotData($todayUB);

    //     $rawPivot = array_merge($rawPivotPrev, $rawPivotToday);

    //     // pivot-ийг Москвагийн цагаар хөрвүүлэх
    //     $pivotTemp = [];
    //     $timeToMoscowDateMap = [];

    //     foreach ($rawPivotPrev as $ubTime => $fidData) {
    //         $ubDateTime = Carbon::createFromFormat('Y-m-d H:i', $prevDayUB->toDateString() . ' ' . $ubTime);
    //         $moscowDateTime = $ubDateTime->copy()->subHours(5);

    //         // Зөвхөн Москвагийн тухайн өдрийн 00:00-18:30-г авах
    //         if ($moscowDateTime->toDateString() === $moscowDate->toDateString()) {
    //             $moscowTime = $moscowDateTime->format('H:i');

    //             $pivotTemp[$moscowTime] = [
    //                 'ub_time' => $ubTime,
    //                 'ub_date' => $prevDayUB->toDateString(),
    //                 'moscow_time' => $moscowTime,
    //                 'moscow_date' => $moscowDateTime->toDateString(),
    //                 'data' => $fidData,
    //             ];

    //             $timeToMoscowDateMap[$moscowTime] = $moscowDateTime->toDateString();
    //         }
    //     }

    //     foreach ($rawPivotToday as $ubTime => $fidData) {

    //         // УБ-гийн цагийг түр холбоно (огноо хамаагүй)
    //         $tmpUb = Carbon::createFromFormat('H:i', $ubTime);

    //         // Москвагийн огноо + цаг
    //         $moscowDateTime = Carbon::createFromFormat(
    //             'Y-m-d H:i',
    //             $moscowDate->toDateString() . ' ' . $tmpUb->copy()->subHours(5)->format('H:i')
    //         );

    //         // Бодит УБ огноо + цаг (энд л шийдэгдэнэ)
    //         $realUbDateTime = $moscowDateTime->copy()->addHours(5);

    //         if ($moscowDateTime->toDateString() === $moscowDate->toDateString()) {

    //             $moscowTime = $moscowDateTime->format('H:i');

    //             $pivotTemp[$moscowTime] = [
    //                 'ub_time' => $realUbDateTime->format('H:i'),
    //                 'ub_date' => $realUbDateTime->toDateString(), // ✅ ЭНД ЗАСАГДАНА
    //                 'moscow_time' => $moscowTime,
    //                 'moscow_date' => $moscowDateTime->toDateString(),
    //                 'data' => $fidData,
    //             ];

    //             $timeToMoscowDateMap[$moscowTime] = $moscowDateTime->toDateString();
    //         }
    //     }


    //     // Москвагийн цагаар эрэмбэлэх
    //     ksort($pivotTemp);
    //     $pivot = $pivotTemp;

    //     // 2. Оросын өгөгдөл - тухайн өдрийн дата
    //     $today = $moscowDate->toDateString();
    //     $tomorrow = $moscowDate->copy()->addDay()->toDateString();

    //     $russianDataRaw = RuFiderDaily::whereRaw('DATE(ognoo) IN (?, ?)', [$today, $tomorrow])
    //         ->whereIn('fider', [257, 258, 110])
    //         ->select('ognoo', 'time_display', 'fider', 'import_kwt', 'export_kwt')
    //         ->get();

    //     // Оросын датаг Москвагийн цаг + огноотой холбох
    //     $russianData = [];
    //     foreach ($pivot as $moscowTime => $timeData) {
    //         $requiredDate = $timeToMoscowDateMap[$moscowTime];

    //         foreach ([257, 258, 110] as $fider) {
    //             $rows = $russianDataRaw->filter(function ($item) use ($requiredDate, $moscowTime, $fider) {
    //                 $itemDate = Carbon::parse($item->ognoo)->format('Y-m-d');
    //                 $itemTime = substr($item->time_display, 0, 5);

    //                 if (strlen($itemTime) == 4 && strpos($itemTime, ':') == 1) {
    //                     $itemTime = '0' . $itemTime;
    //                 }

    //                 return $itemDate === $requiredDate
    //                     && $itemTime === $moscowTime
    //                     && $item->fider == $fider;
    //             });

    //             if (!isset($russianData[$moscowTime])) {
    //                 $russianData[$moscowTime] = [];
    //             }
    //             if (!isset($russianData[$moscowTime][$fider])) {
    //                 $russianData[$moscowTime][$fider] = [];
    //             }

    //             if ($rows->count() > 0) {
    //                 $totalImport = $rows->sum('import_kwt');
    //                 $totalExport = $rows->sum('export_kwt');

    //                 $russianData[$moscowTime][$fider][0] = (object)[
    //                     'import_kwt' => $totalImport,
    //                     'export_kwt' => $totalExport,
    //                 ];
    //             } else {
    //                 $russianData[$moscowTime][$fider][0] = (object)[
    //                     'import_kwt' => 0,
    //                     'export_kwt' => 0,
    //                 ];
    //             }
    //         }
    //     }

    //     // Debug мэдээлэл
    //     $debug = [
    //         'total_records' => BufFiderDaily::forDate($prevDayUB)->count() + BufFiderDaily::forDate($todayUB)->count(),
    //         'russian_records' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$today])
    //             ->whereIn('fider', [257, 258, 110])
    //             ->count(),
    //         'russian_tomorrow_records' => RuFiderDaily::whereRaw('DATE(ognoo) = ?', [$tomorrow])
    //             ->whereIn('fider', [257, 258, 110])
    //             ->count(),
    //         'pivot_moscow_times' => array_slice(array_keys($pivot), 0, 10),
    //         'pivot_sample' => array_slice($pivot, 0, 3, true),
    //     ];

    //     return view('bufvint.today', compact(
    //         'pivot',
    //         'russianData',
    //         'moscowDate',
    //         'debug'
    //     ))->with('carbonDate', $moscowDate); // Backward compatibility
    // }

    public function todayData(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        try {
            // Сонгосон Москвагийн огноо
            $moscowDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $moscowDate = Carbon::today();
        }

        /*
     |--------------------------------------------------------------------------
     | 1. Монголын 3 өдрийн дата татна
     |--------------------------------------------------------------------------
     */
        $prevDayUB = $moscowDate->copy()->subDay(); // УБ өмнөх
        $todayUB   = $moscowDate->copy();           // УБ тухайн
        $nextDayUB = $moscowDate->copy()->addDay(); // УБ маргааш

        $rawPivotPrev  = BufFiderDaily::getPivotData($prevDayUB);
        $rawPivotToday = BufFiderDaily::getPivotData($todayUB);
        $rawPivotNext  = BufFiderDaily::getPivotData($nextDayUB);

        $allRawPivot = [
            $prevDayUB->toDateString()  => $rawPivotPrev,
            $todayUB->toDateString()    => $rawPivotToday,
            $nextDayUB->toDateString()  => $rawPivotNext,
        ];

        /*
     |--------------------------------------------------------------------------
     | 2. Монгол → Москва pivot үүсгэх
     |--------------------------------------------------------------------------
     */
        $pivotTemp = [];
        $timeToMoscowDateMap = [];

        foreach ($allRawPivot as $ubDate => $rawPivotDay) {
            foreach ($rawPivotDay as $ubTime => $fidData) {

                // Бодит УБ datetime
                $ubDateTime = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $ubDate . ' ' . $ubTime
                );

                // Москвагийн datetime
                $moscowDateTime = $ubDateTime->copy()->subHours(5);

                // Зөвхөн сонгосон Москвагийн өдөр
                if ($moscowDateTime->toDateString() !== $moscowDate->toDateString()) {
                    continue;
                }

                $moscowTime = $moscowDateTime->format('H:i');

                $pivotTemp[$moscowTime] = [
                    'ub_time'     => $ubDateTime->format('H:i'),
                    'ub_date'     => $ubDateTime->toDateString(),   // ✅ 100% зөв
                    'moscow_time' => $moscowTime,
                    'moscow_date' => $moscowDateTime->toDateString(),
                    'data'        => $fidData,
                ];

                $timeToMoscowDateMap[$moscowTime] = $moscowDateTime->toDateString();
            }
        }

        // Москвагийн цагаар эрэмбэлэх
        ksort($pivotTemp);
        $pivot = $pivotTemp;

        /*
     |--------------------------------------------------------------------------
     | 3. Оросын өгөгдөл (Москвагийн огноо + маргааш)
     |--------------------------------------------------------------------------
     */
        $today    = $moscowDate->toDateString();
        $tomorrow = $moscowDate->copy()->addDay()->toDateString();

        $russianDataRaw = RuFiderDaily::whereRaw(
            'DATE(ognoo) IN (?, ?)',
            [$today, $tomorrow]
        )
            ->whereIn('fider', [257, 258, 110])
            ->select('ognoo', 'time_display', 'fider', 'import_kwt', 'export_kwt')
            ->get();

        /*
     |--------------------------------------------------------------------------
     | 4. Оросын дата pivot-той тааруулах
     |--------------------------------------------------------------------------
     */
        $russianData = [];

        foreach ($pivot as $moscowTime => $timeData) {

            $requiredDate = $timeToMoscowDateMap[$moscowTime];

            foreach ([257, 258, 110] as $fider) {

                $rows = $russianDataRaw->filter(function ($item) use ($requiredDate, $moscowTime, $fider) {
                    $itemDate = Carbon::parse($item->ognoo)->format('Y-m-d');
                    $itemTime = substr($item->time_display, 0, 5);

                    if (strlen($itemTime) === 4) {
                        $itemTime = '0' . $itemTime;
                    }

                    return $itemDate === $requiredDate
                        && $itemTime === $moscowTime
                        && $item->fider == $fider;
                });

                if (!isset($russianData[$moscowTime][$fider])) {
                    $russianData[$moscowTime][$fider][0] = (object)[
                        'import_kwt' => 0,
                        'export_kwt' => 0,
                    ];
                }

                if ($rows->count() > 0) {
                    $russianData[$moscowTime][$fider][0] = (object)[
                        'import_kwt' => $rows->sum('import_kwt'),
                        'export_kwt' => $rows->sum('export_kwt'),
                    ];
                }
            }
        }

        /*
     |--------------------------------------------------------------------------
     | 5. Debug
     |--------------------------------------------------------------------------
     */
        $debug = [
            'ub_prev'  => $prevDayUB->toDateString(),
            'ub_today' => $todayUB->toDateString(),
            'ub_next'  => $nextDayUB->toDateString(),
            'pivot_times_sample' => array_slice(array_keys($pivot), 0, 10),
        ];

        return view('bufvint.today', compact(
            'pivot',
            'russianData',
            'moscowDate',
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
