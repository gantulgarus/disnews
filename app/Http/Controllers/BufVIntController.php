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
            $moscowDate = Carbon::parse($date); // Энэ нь МОСКВАГИЙН огноо
        } catch (\Exception $e) {
            $moscowDate = Carbon::today();
        }

        // Москвагийн тухайн өдрийн 00:00-23:30-г харуулахын тулд:
        // 1. УБ өмнөх өдрийн 05:00-23:30 (Москва тухайн өдрийн 00:00-18:30)
        // 2. УБ тухайн өдрийн 00:00-04:30 (Москва өмнөх өдрийн 19:00-23:30) ← ЭНД АЛДАА БАЙНА
        // 3. Зөв нь: УБ тухайн өдрийн 00:00-04:30 (Москва өмнөх өдрийн 19:00-23:30)

        $prevDayUB = $moscowDate->copy()->subDay(); // УБ өмнөх өдөр
        $todayUB = $moscowDate->copy(); // УБ тухайн өдөр

        // 1. Монголын өгөгдөл - хоёр өдрийн дата
        $rawPivotPrev = BufFiderDaily::getPivotData($prevDayUB);
        $rawPivotToday = BufFiderDaily::getPivotData($todayUB);

        // pivot-ийг Москвагийн цагаар хөрвүүлэх
        $pivot = [];
        $timeToMoscowDateMap = [];

        // А. УБ өмнөх өдрийн 05:00-23:30 → Москва тухайн өдрийн 00:00-18:30
        foreach ($rawPivotPrev as $ubTime => $fidData) {
            $ubDateTime = Carbon::createFromFormat('Y-m-d H:i', $prevDayUB->toDateString() . ' ' . $ubTime);
            $moscowDateTime = $ubDateTime->copy()->subHours(5);

            // Зөвхөн Москвагийн тухайн өдрийн 00:00-18:30-г авах
            if ($moscowDateTime->toDateString() === $moscowDate->toDateString()) {
                $moscowTime = $moscowDateTime->format('H:i');

                $pivot[$moscowTime] = [
                    'ub_time' => $ubTime,
                    'ub_date' => $prevDayUB->toDateString(),
                    'moscow_time' => $moscowTime,
                    'moscow_date' => $moscowDate->toDateString(), // Москвагийн тухайн өдөр
                    'data' => $fidData,
                ];

                $timeToMoscowDateMap[$moscowTime] = $moscowDate->toDateString();
            }
        }

        // Б. УБ тухайн өдрийн 00:00-04:30 → Москва өмнөх өдрийн 19:00-23:30
        foreach ($rawPivotToday as $ubTime => $fidData) {
            $ubDateTime = Carbon::createFromFormat('Y-m-d H:i', $todayUB->toDateString() . ' ' . $ubTime);
            $moscowDateTime = $ubDateTime->copy()->subHours(5);

            // Зөвхөн 00:00-04:30 цагийн хуваарь
            if (in_array($ubTime, ['00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30'])) {
                // Эдгээр нь Москвагийн өмнөх өдрийн 19:00-23:30 байна
                $moscowTime = $moscowDateTime->format('H:i');
                $moscowDateForThisTime = $moscowDate->copy()->subDay()->toDateString(); // Москвагийн өмнөх өдөр

                $pivot[$moscowTime] = [
                    'ub_time' => $ubTime,
                    'ub_date' => $todayUB->toDateString(),
                    'moscow_time' => $moscowTime,
                    'moscow_date' => $moscowDateForThisTime, // Москвагийн өмнөх өдөр
                    'data' => $fidData,
                ];

                $timeToMoscowDateMap[$moscowTime] = $moscowDateForThisTime;
            }
        }

        // Москвагийн цагаар эрэмбэлэх (00:00-23:30)
        ksort($pivot);

        // 2. Оросын өгөгдөл - Москвагийн цагаар шууд
        $russianData = [];

        // Москвагийн тухайн өдрийн бүх цаг (00:00-23:30)
        for ($i = 1; $i <= 48; $i++) {
            $hour = str_pad(floor(($i - 1) / 2), 2, '0', STR_PAD_LEFT);
            $min = ($i % 2 == 1) ? '00' : '30';
            $moscowTime = "{$hour}:{$min}";

            // Оросын өгөгдөл: Москвагийн тухайн өдрийн цаг бүрт
            $requiredDate = $moscowDate->toDateString();

            foreach ([257, 258, 110] as $fider) {
                $rows = RuFiderDaily::where('ognoo', $requiredDate)
                    ->where('fider', $fider)
                    ->where('time_display', $moscowTime)
                    ->select('import_kwt', 'export_kwt')
                    ->get();

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
            'total_records' => BufFiderDaily::forDate($prevDayUB)->count() + BufFiderDaily::forDate($todayUB)->count(),
            'russian_records' => RuFiderDaily::where('ognoo', $moscowDate->toDateString())
                ->whereIn('fider', [257, 258, 110])
                ->count(),
            'pivot_moscow_times' => array_slice(array_keys($pivot), 0, 10),
            'pivot_sample' => array_slice($pivot, 0, 3, true),
            'moscow_date' => $moscowDate->toDateString(),
            'prev_day_ub' => $prevDayUB->toDateString(),
            'today_ub' => $todayUB->toDateString(),
        ];

        return view('bufvint.today', compact(
            'pivot',
            'russianData',
            'moscowDate',
            'debug'
        ))->with('carbonDate', $moscowDate);
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
