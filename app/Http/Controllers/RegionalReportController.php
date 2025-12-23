<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ZConclusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionalReportController extends Controller
{

    public function index(Request $request)
    {

        $selectedMonth = $request->month ?? now()->format('Y-m');


        $carbon = Carbon::createFromFormat('Y-m', $selectedMonth);

        $yearCurrent  = $carbon->year;          // 2025
        $month        = $carbon->month;         // сонгосон сар
        $yearPrevious = $yearCurrent - 1;       // 2024

        // тухайн сарын бодит хоног
        $daysInMonth = $carbon->daysInMonth;

        $vars = [
            'A_ULAANBAATAR_CITY_TOTAL_P' => 'Улаанбаатар',
            'A_DARKHAN_SELENGE_TOTAL_P' => 'Дархан Сэлэнгэ',
            'A_ERDENET_BULGAN_TOTAL_P' => 'Эрдэнэт Булган',
            'A_BAGANUUR_CHOIR_TOTAL_P' => 'Багануур Зүүн өмнөд бүс',
            'A_GOVI_TOTAL_P' => 'Говь'
        ];

        // ОДООГИЙН ОН – СОНГОСОН САР

        $data = []; // бүх VAR-ийн үр дүнг цуглуулах array
        foreach ($vars as $var => $stationName) {

            $queryCurrent = "SELECT
                                'CURRENT' as row_type,
                                 VAR";

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                $queryCurrent .= ",
                    MAX(CASE
                        WHEN DAY(FROM_UNIXTIME(timestamp_s)) = $i
                        THEN VALUE
                    END) AS d$day";
            }

            $queryCurrent .= "
                FROM z_conclusion
                WHERE YEAR(FROM_UNIXTIME(timestamp_s)) = ?
                AND MONTH(FROM_UNIXTIME(timestamp_s)) = ?
                AND VAR = ?
                GROUP BY VAR
            ";

            //ӨМНӨХ ОН – МӨН САР
            $queryPrevious = "SELECT
                                'PREVIOUS' as row_type,
                                 VAR";

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                $queryPrevious .= ",
                    MAX(CASE
                        WHEN DAY(FROM_UNIXTIME(timestamp_s)) = $i
                        THEN VALUE
                    END) AS d$day";
            }

            $queryPrevious .= "
                FROM z_conclusion
                WHERE YEAR(FROM_UNIXTIME(timestamp_s)) = ?
                AND MONTH(FROM_UNIXTIME(timestamp_s)) = ?
                AND VAR = ?
                GROUP BY VAR
            ";

            // Query-г нийлүүлэх
            $finalQuery = $queryCurrent . " UNION ALL " . $queryPrevious;

            $result = DB::select(
                $finalQuery,
                [$yearCurrent, $month, $var, $yearPrevious, $month, $var]
            );
            $data[$var] = $result;
        }


        $days = range(1, $daysInMonth);

        $sum2024 = [];
        $sum2025 = [];
        $diff    = [];

        foreach ($days as $d) {
            $dayKey = 'd' . str_pad($d, 2, '0', STR_PAD_LEFT);

            $total2024 = 0;
            $total2025 = 0;

            foreach ($data as $rows) {
                $rows = collect($rows);
                $prev = $rows->firstWhere('row_type', 'PREVIOUS');
                $curr = $rows->firstWhere('row_type', 'CURRENT');

                $total2024 += $prev->$dayKey ?? 0;
                $total2025 += $curr->$dayKey ?? 0;
            }

            $sum2024[] = round($total2024, 2);
            $sum2025[] = round($total2025, 2);
            $diff[]    = round($total2025 - $total2024, 2);
        }

        // 🔹 3. View рүү дамжуулах
        $days = range(1, $daysInMonth);

        return view('reports.Regional', compact(

            'data',
            'selectedMonth',
            'month',
            'daysInMonth',
            'yearCurrent',
            'yearPrevious',
            'vars',

            'days',
            'sum2024',
            'sum2025',
            'diff'
        ));
    }
}