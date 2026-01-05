<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BufVInt;
use Illuminate\Http\Request;
use App\Models\BufFiderDaily;
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