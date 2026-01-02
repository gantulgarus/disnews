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

        $data = DB::table('buf_v_int')
            ->select(
                DB::raw('LPAD(FLOOR((N_INTER_RAS - 1)/2), 2, "0") as hour'),
                'N_INTER_RAS',
                DB::raw('SUM(CASE WHEN N_FID = 257 AND N_GR_TY = 2 THEN VAL ELSE 0 END) as import_257'),
                DB::raw('SUM(CASE WHEN N_FID = 257 AND N_GR_TY = 1 THEN VAL ELSE 0 END) as export_257'),
                DB::raw('SUM(CASE WHEN N_FID = 258 AND N_GR_TY = 2 THEN VAL ELSE 0 END) as import_258'),
                DB::raw('SUM(CASE WHEN N_FID = 258 AND N_GR_TY = 1 THEN VAL ELSE 0 END) as export_258'),
                DB::raw('SUM(CASE WHEN N_FID = 110 AND N_GR_TY = 2 THEN VAL ELSE 0 END) as import_110'),
                DB::raw('SUM(CASE WHEN N_FID = 110 AND N_GR_TY = 1 THEN VAL ELSE 0 END) as export_110')
            )
            ->whereIn('N_FID', [257, 258, 110])
            ->whereIn('N_GR_TY', [1, 2])
            ->whereRaw('DATE(DD_MM_YYYY) = CURDATE()')
            ->groupBy('N_INTER_RAS')
            ->orderBy('N_INTER_RAS')
            ->get();


        return view('bufvint.today', compact('data'));
    }
}