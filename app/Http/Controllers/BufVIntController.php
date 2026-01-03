<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BufVInt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $data = DB::table('buf_v_int')
            ->select(
                'N_INTER_RAS',
                DB::raw('CONCAT(
                LPAD(FLOOR((N_INTER_RAS-1)/2), 2, "0"), ":",
                CASE WHEN MOD(N_INTER_RAS, 2) = 1 THEN "00" ELSE "30" END, "-",
                LPAD(FLOOR((N_INTER_RAS-1)/2), 2, "0"), ":",
                CASE WHEN MOD(N_INTER_RAS, 2) = 1 THEN "30" ELSE "00" END
            ) as TIME_DISPLAY'),
                'N_FID',
                DB::raw('ROUND(SUM(CASE WHEN N_GR_TY = 2 THEN VAL ELSE 0 END), 2) as IMPORT_KWT'),
                DB::raw('ROUND(SUM(CASE WHEN N_GR_TY = 1 THEN VAL ELSE 0 END), 2) as EXPORT_KWT')
            )
            ->whereIn('N_GR_TY', [1, 2])
            ->whereIn('N_FID', [257, 258, 110])
            ->whereRaw('DATE(DD_MM_YYYY) = ?', [$carbonDate->toDateString()])
            ->groupBy('N_INTER_RAS', 'N_FID')
            ->orderBy('N_INTER_RAS')
            ->orderBy('N_FID')
            ->get();

        // Бүх цагийн интервал үүсгэх (00:00-00:30 ... 23:30-00:00)
        $allTimes = [];
        for ($i = 1; $i <= 48; $i++) {
            $hour = str_pad(floor(($i - 1) / 2), 2, '0', STR_PAD_LEFT);
            $startMin = ($i % 2 == 1) ? '00' : '30';
            $endMin = ($i % 2 == 1) ? '30' : '00';
            $allTimes[] = "{$hour}:{$startMin}-{$hour}:{$endMin}";
        }

        // Pivot үүсгэх - бүх цаг, бүх фидертэй
        $pivot = [];
        foreach ($allTimes as $time) {
            $pivot[$time] = [
                257 => ['IMPORT' => 0, 'EXPORT' => 0],
                258 => ['IMPORT' => 0, 'EXPORT' => 0],
                110 => ['IMPORT' => 0, 'EXPORT' => 0],
            ];
        }

        // Өгөгдлөөр дарж бичих
        foreach ($data as $row) {
            $time = $row->TIME_DISPLAY;
            $fid = (int)$row->N_FID;

            if (isset($pivot[$time][$fid])) {
                $pivot[$time][$fid] = [
                    'IMPORT' => (float)$row->IMPORT_KWT,
                    'EXPORT' => (float)$row->EXPORT_KWT
                ];
            }
        }

        // Debug
        $debug = [
            'query_count' => $data->count(),
            'fiders_found' => $data->pluck('N_FID')->unique()->values()->toArray(),
        ];

        return view('bufvint.today', compact('pivot', 'carbonDate', 'debug'));
    }
}
