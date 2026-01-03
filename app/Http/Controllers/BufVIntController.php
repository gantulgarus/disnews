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
        // Огноо параметр авах (default: өнөөдөр)
        $date = $request->input('date', Carbon::today()->toDateString());

        // Огнооны валидаци
        try {
            $carbonDate = Carbon::parse($date);
        } catch (\Exception $e) {
            $carbonDate = Carbon::today();
        }

        $data = DB::table('buf_v_int')
            ->select(
                DB::raw('DATE(DD_MM_YYYY) as OGNOO'),
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
            ->whereRaw('DATE(DD_MM_YYYY) = ?', [$carbonDate->toDateString()])  // ОГНОО FILTER
            ->groupBy('N_INTER_RAS', 'N_FID', DB::raw('DATE(DD_MM_YYYY)'))
            ->orderBy('N_INTER_RAS')
            ->orderBy('N_FID')
            ->get();

        // Pivot хэлбэрт хувиргах
        $pivot = [];
        foreach ($data as $row) {
            $time = $row->TIME_DISPLAY;
            $fid = $row->N_FID;
            $pivot[$time][$fid] = [
                'IMPORT' => $row->IMPORT_KWT,
                'EXPORT' => $row->EXPORT_KWT
            ];
        }

        return view('bufvint.today', compact('pivot', 'carbonDate'));
    }
}