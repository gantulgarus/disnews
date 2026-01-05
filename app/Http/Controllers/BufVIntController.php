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

        // pivot-ийн key-г Москвагийн цагт хөрвүүлж, шаардлагатай огнооны дата татах
        $pivot = [];
        $russianDates = []; // Оросын аль огноонуудын дата хэрэгтэй болохыг бүртгэх

        foreach ($rawPivot as $ubTime => $fidData) {
            // Монголын цаг → Москвагийн цаг
            $moscowDateTime = Carbon::createFromFormat('Y-m-d H:i', $carbonDate->toDateString() . ' ' . $ubTime)
                ->subHours(5);

            $moscowTime = $moscowDateTime->format('H:i');
            $moscowDate = $moscowDateTime->toDateString();

            $pivot[$moscowTime] = $fidData;

            // Энэ Москвагийн цагт ямар огнооны дата хэрэгтэй болохыг бүртгэх
            if (!isset($russianDates[$moscowTime])) {
                $russianDates[$moscowTime] = $moscowDate;
            }
        }

        // 2. Оросын өгөгдөл - тухайн болон өмнөх өдрийн датаг авах
        $yesterday = $carbonDate->copy()->subDay()->toDateString();
        $today = $carbonDate->toDateString();

        $russianDataRaw = RuFiderDaily::whereIn('ognoo', [$yesterday, $today])
            ->whereIn('fider', [257, 258, 110])
            ->selectRaw('
                ognoo,
                time_display,
                fider,
                SUM(import_kwt) as import_kwt,
                SUM(export_kwt) as export_kwt
            ')
            ->groupBy('ognoo', 'time_display', 'fider')
            ->get();

        // Оросын датаг зөв огноотой нь холбох
        $russianData = [];
        foreach ($russianDataRaw as $row) {
            $timeKey = $row->time_display;
            $fider = $row->fider;

            // Энэ цагт ямар огнооны дата хэрэгтэй болохыг шалгах
            $requiredDate = $russianDates[$timeKey] ?? $today;

            // Зөв огноотой бол хадгалах
            if ($row->ognoo === $requiredDate) {
                if (!isset($russianData[$timeKey])) {
                    $russianData[$timeKey] = [];
                }
                if (!isset($russianData[$timeKey][$fider])) {
                    $russianData[$timeKey][$fider] = [];
                }
                $russianData[$timeKey][$fider][] = $row;
            }
        }

        // Debug мэдээлэл
        $debug = [
            'total_records' => BufFiderDaily::forDate($carbonDate)->count(),
            'fiders_in_db' => BufFiderDaily::forDate($carbonDate)->distinct('FIDER')->pluck('FIDER')->toArray(),
            'russian_records' => RuFiderDaily::where('ognoo', $carbonDate->toDateString())
                ->whereIn('fider', [257, 258, 110])
                ->count(),
            'russian_yesterday_records' => RuFiderDaily::where('ognoo', $yesterday)
                ->whereIn('fider', [257, 258, 110])
                ->count(),
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

        // ru_xml folder байхгүй бол үүсгэх
        $storagePath = storage_path('app/ru_xml');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Файл хадгалах
        $path = $request->file('xml_file')->store('ru_xml');
        $fullPath = storage_path('app/private/' . $path);

        if (!file_exists($fullPath)) {
            return back()->withErrors('Файл хадгалагдсангүй: ' . $fullPath);
        }

        // Импорт хийх
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
