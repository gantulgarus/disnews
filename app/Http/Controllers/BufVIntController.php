<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BufVInt;
use Illuminate\Http\Request;
use App\Models\BufFiderDaily;
use Illuminate\Support\Facades\DB;

class BufVIntController extends Controller
{
    // public function todayData(Request $request)
    // {
    //     $date = $request->input('date', Carbon::today()->toDateString());

    //     try {
    //         $carbonDate = Carbon::parse($date);
    //     } catch (\Exception $e) {
    //         $carbonDate = Carbon::today();
    //     }

    //     // JOIN ЗААВАЛ ХЭРЭГТЭЙ - buf_v_int-д N_FID байхгүй!
    //     $data = DB::table('buf_v_int as buf')
    //         ->join('gr_gr as gr', function ($join) {
    //             $join->on('buf.N_OB', '=', 'gr.N_OB')
    //                 ->on('buf.SYB_RNK', '=', 'gr.SYB_RNK')
    //                 ->on('buf.N_SH', '=', 'gr.N_SH');  // ЖИЖИГ n_sh биш, том N_SH
    //         })
    //         ->select(
    //             'buf.N_INTER_RAS',
    //             DB::raw('CONCAT(
    //                 LPAD(FLOOR((buf.N_INTER_RAS-1)/2), 2, "0"), ":",
    //                 CASE WHEN MOD(buf.N_INTER_RAS, 2) = 1 THEN "00" ELSE "30" END, "-",
    //                 LPAD(FLOOR((buf.N_INTER_RAS-1)/2), 2, "0"), ":",
    //                 CASE WHEN MOD(buf.N_INTER_RAS, 2) = 1 THEN "30" ELSE "00" END
    //             ) as TIME_DISPLAY'),
    //             'gr.N_FID',
    //             DB::raw('ROUND(SUM(CASE WHEN buf.N_GR_TY = 2 THEN buf.VAL ELSE 0 END), 2) as IMPORT_KWT'),
    //             DB::raw('ROUND(SUM(CASE WHEN buf.N_GR_TY = 1 THEN buf.VAL ELSE 0 END), 2) as EXPORT_KWT')
    //         )
    //         ->whereIn('buf.N_GR_TY', [1, 2])
    //         ->where('gr.ZNAK', '=', 1)
    //         ->whereIn('gr.N_FID', [257, 258, 110])
    //         ->whereRaw('DATE(buf.DD_MM_YYYY) = ?', [$carbonDate->toDateString()])
    //         ->groupBy(
    //             'buf.N_INTER_RAS',
    //             'buf.N_OB',        // GROUP BY-д нэмэх
    //             'buf.SYB_RNK',     // GROUP BY-д нэмэх
    //             'gr.N_FID'
    //         )
    //         ->orderBy('buf.N_INTER_RAS')
    //         ->orderBy('gr.N_FID')
    //         ->get();

    //     // Бүх цагийн интервал үүсгэх
    //     $allTimes = [];
    //     for ($i = 1; $i <= 48; $i++) {
    //         $hour = str_pad(floor(($i - 1) / 2), 2, '0', STR_PAD_LEFT);
    //         $startMin = ($i % 2 == 1) ? '00' : '30';
    //         $endMin = ($i % 2 == 1) ? '30' : '00';
    //         $allTimes[] = "{$hour}:{$startMin}-{$hour}:{$endMin}";
    //     }

    //     // Pivot үүсгэх - бүх цаг, бүх фидертэй
    //     $pivot = [];
    //     foreach ($allTimes as $time) {
    //         $pivot[$time] = [
    //             257 => ['IMPORT' => 0, 'EXPORT' => 0],
    //             258 => ['IMPORT' => 0, 'EXPORT' => 0],
    //             110 => ['IMPORT' => 0, 'EXPORT' => 0],
    //         ];
    //     }

    //     // Өгөгдлөөр дарж бичих
    //     foreach ($data as $row) {
    //         $time = $row->TIME_DISPLAY;
    //         $fid = (int)$row->N_FID;

    //         if (isset($pivot[$time][$fid])) {
    //             // Өмнөх утгуудыг НЭМЭХ (давхцсан тоолууруудыг нэгтгэх)
    //             $pivot[$time][$fid]['IMPORT'] += (float)$row->IMPORT_KWT;
    //             $pivot[$time][$fid]['EXPORT'] += (float)$row->EXPORT_KWT;
    //         }
    //     }

    //     // Debug мэдээлэл
    //     $debug = [
    //         'query_count' => $data->count(),
    //         'fiders_found' => $data->pluck('N_FID')->unique()->values()->toArray(),
    //         'sample_data' => $data->take(3)->toArray(),
    //     ];

    //     return view('bufvint.today', compact('pivot', 'carbonDate', 'debug'));
    // }

    public function todayData(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        try {
            $carbonDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $carbonDate = Carbon::today();
        }

        // Model static method ашиглах - хамгийн хялбар!
        $pivot = BufFiderDaily::getPivotData($carbonDate);

        // Debug мэдээлэл
        $debug = [
            'total_records' => BufFiderDaily::forDate($carbonDate)->count(),
            'fiders_in_db' => BufFiderDaily::forDate($carbonDate)->distinct('FIDER')->pluck('FIDER')->toArray(),
        ];

        return view('bufvint.today', compact('pivot', 'carbonDate', 'debug'));
    }
}
