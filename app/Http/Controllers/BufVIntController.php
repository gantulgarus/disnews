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

        $data = DB::table('buf_v_int as buf')
            ->join('gr_gr as gr', function ($join) {
                $join->on('buf.N_OB', '=', 'gr.N_OB')
                    ->on('buf.SYB_RNK', '=', 'gr.SYB_RNK')
                    ->on('buf.n_sh', '=', 'gr.n_sh');
            })
            ->select(
                DB::raw('DATE(buf.DD_MM_YYYY) as OGNOO'),
                'buf.N_INTER_RAS',
                DB::raw('CONCAT(
                LPAD(FLOOR((buf.N_INTER_RAS-1)/2), 2, "0"), ":",
                CASE WHEN MOD(buf.N_INTER_RAS, 2) = 1 THEN "00" ELSE "30" END, "-",
                LPAD(FLOOR((buf.N_INTER_RAS-1)/2), 2, "0"), ":",
                CASE WHEN MOD(buf.N_INTER_RAS, 2) = 1 THEN "30" ELSE "00" END
            ) as TIME_DISPLAY'),
                'gr.N_FID',
                DB::raw('ROUND(SUM(CASE WHEN buf.N_GR_TY = 2 THEN buf.VAL ELSE 0 END), 2) as IMPORT_KWT'),
                DB::raw('ROUND(SUM(CASE WHEN buf.N_GR_TY = 1 THEN buf.VAL ELSE 0 END), 2) as EXPORT_KWT')
            )
            ->whereIn('buf.N_GR_TY', [1, 2])
            ->where('gr.ZNAK', '=', 1)
            ->whereIn('gr.N_FID', [257, 258, 110])
            ->whereRaw('DATE(buf.DD_MM_YYYY) = ?', [$carbonDate->toDateString()])
            ->groupBy(
                DB::raw('DATE(buf.DD_MM_YYYY)'),
                'buf.N_INTER_RAS',
                'gr.N_FID'
            )
            ->orderBy('buf.N_INTER_RAS')
            ->orderBy('gr.N_FID')
            ->get();

        // DEBUG: Query-н үр дүнг харах
        dd([
            'data_count' => $data->count(),
            'first_5_rows' => $data->take(5),
            'sample_row' => $data->first()
        ]);
    }
}
