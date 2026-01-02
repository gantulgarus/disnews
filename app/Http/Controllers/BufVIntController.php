<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BufVInt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BufVIntController extends Controller
{
    public function todayData()
    {
        $today = Carbon::today()->toDateString();

        $data = BufVInt::select(
            'DD_MM_YYYY as OGNOO',
            'N_INTER_RAS as TIME_INTERVAL',
            DB::raw("TO_CHAR(TRUNC((N_INTER_RAS - 1) / 2), 'FM00') || ':' ||
                        CASE WHEN MOD(N_INTER_RAS,2)=1 THEN '00' ELSE '30' END || '-' ||
                        TO_CHAR(TRUNC((N_INTER_RAS - 1) / 2), 'FM00') || ':' ||
                        CASE WHEN MOD(N_INTER_RAS,2)=1 THEN '30' ELSE '00' END AS TIME_DISPLAY"),
            'N_OB as OBEKT',
            'SYB_RNK as SULJEE',
            'N_FID as FIDER',
            DB::raw("CASE
                    WHEN N_FID = 227 THEN 'АШ 227'
                    WHEN N_FID = 228 THEN 'АШ 228'
                    WHEN N_FID = 110 THEN 'Тойт 110'
                    ELSE 'Бусад шугам'
                END AS LINE_NAME"),
            DB::raw("ROUND(SUM(CASE WHEN N_GR_TY = 2 THEN VAL ELSE 0 END),2) AS IMPORT_KWT"),
            DB::raw("ROUND(SUM(CASE WHEN N_GR_TY = 1 THEN VAL ELSE 0 END),2) AS EXPORT_KWT"),
            DB::raw("COUNT(DISTINCT N_SH) AS TOOTSOOLUUR_COUNT")
        )
            ->whereRaw('DD_MM_YYYY = TRUNC(SYSDATE)')
            ->whereIn('N_FID', [227, 228, 110])
            ->whereIn('N_GR_TY', [1, 2])
            ->groupBy('DD_MM_YYYY', 'N_INTER_RAS', 'N_OB', 'SYB_RNK', 'N_FID')
            ->orderBy('N_INTER_RAS')
            ->get();

        return view('bufvint.today', compact('data'));
    }
}
