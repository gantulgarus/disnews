<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\DailyBalanceBattery;
use App\Models\DailyBalanceJournal;
use App\Models\DailyBalanceImportExport;

class DailyBalanceJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // index() эхэнд
        // dd($request->all());

        $month = $request->input('month') ?? date('Y-m');
        [$year, $mon] = explode('-', $month);

        $userOrgId = auth()->user()->organization_id;

        $query = DailyBalanceJournal::with('powerPlant')
            ->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            })
            ->whereYear('date', $year)
            ->whereMonth('date', $mon);

        // 👉 Хэрэв станц сонгосон бол зөвхөн тэр станцын мэдээллийг үзүүлэх
        if ($request->filled('plant_id')) {
            $query->where('power_plant_id', intval($request->plant_id));
        }

        $journals = $query->orderBy('date', 'asc')->get();

        return view('daily_balance_journals.index', [
            'journals' => $journals,
            'month' => $month,
            'selectedPlant' => $request->plant_id
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        $powerPlant = $user->mainPowerPlant;

        return view('daily_balance_journals.create', compact('powerPlant'));
    }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'processed_amount' => 'nullable|numeric',
            'distribution_amount' => 'nullable|numeric',
            'internal_demand' => 'nullable|numeric',
            'percent' => 'nullable|numeric',

            // 3 цагийн интервалын талбарууд
            'positive_deviation_00_08' => 'nullable|numeric',
            'positive_deviation_08_16' => 'nullable|numeric',
            'positive_deviation_16_24' => 'nullable|numeric',

            'negative_deviation_spot_00_08' => 'nullable|numeric',
            'negative_deviation_spot_08_16' => 'nullable|numeric',
            'negative_deviation_spot_16_24' => 'nullable|numeric',

            'negative_deviation_import_00_08' => 'nullable|numeric',
            'negative_deviation_import_08_16' => 'nullable|numeric',
            'negative_deviation_import_16_24' => 'nullable|numeric',

            'positive_resolution_00_08' => 'nullable|numeric',
            'positive_resolution_08_16' => 'nullable|numeric',
            'positive_resolution_16_24' => 'nullable|numeric',

            'negative_resolution_00_08' => 'nullable|numeric',
            'negative_resolution_08_16' => 'nullable|numeric',
            'negative_resolution_16_24' => 'nullable|numeric',

            'deviation_reason' => 'nullable|string',
            'by_consumption_growth' => 'nullable|numeric',
            'by_other_station_issue' => 'nullable|numeric',
            'dispatcher_name' => 'required|string|max:255',
        ]);

        // Хэрэглэгчийн үндсэн цахилгаан станцын ID-г автоматаар нэмэх
        $validated['power_plant_id'] = auth()->user()->mainPowerPlant->id;

        DailyBalanceJournal::create($validated);

        return redirect()->route('daily-balance-journals.index')->with('success', 'Мэдээ амжилттай үүслээ.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyBalanceJournal $dailyBalanceJournal)
    {
        return view('daily_balance_journals.show', compact('dailyBalanceJournal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyBalanceJournal $dailyBalanceJournal)
    {
        $user = auth()->user();

        $powerPlants = $user->mainPowerPlant;

        return view('daily_balance_journals.edit', compact('dailyBalanceJournal', 'powerPlants'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DailyBalanceJournal  $dailyBalanceJournal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, DailyBalanceJournal $dailyBalanceJournal)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'processed_amount' => 'nullable|numeric',
            'distribution_amount' => 'nullable|numeric',
            'internal_demand' => 'nullable|numeric',
            'percent' => 'nullable|numeric',

            // 3 цагийн интервалын талбарууд
            'positive_deviation_00_08' => 'nullable|numeric',
            'positive_deviation_08_16' => 'nullable|numeric',
            'positive_deviation_16_24' => 'nullable|numeric',

            'negative_deviation_spot_00_08' => 'nullable|numeric',
            'negative_deviation_spot_08_16' => 'nullable|numeric',
            'negative_deviation_spot_16_24' => 'nullable|numeric',

            'negative_deviation_import_00_08' => 'nullable|numeric',
            'negative_deviation_import_08_16' => 'nullable|numeric',
            'negative_deviation_import_16_24' => 'nullable|numeric',

            'positive_resolution_00_08' => 'nullable|numeric',
            'positive_resolution_08_16' => 'nullable|numeric',
            'positive_resolution_16_24' => 'nullable|numeric',

            'negative_resolution_00_08' => 'nullable|numeric',
            'negative_resolution_08_16' => 'nullable|numeric',
            'negative_resolution_16_24' => 'nullable|numeric',

            'deviation_reason' => 'nullable|string',
            'by_consumption_growth' => 'nullable|numeric',
            'by_other_station_issue' => 'nullable|numeric',
            'dispatcher_name' => 'required|string|max:255',
        ]);

        $dailyBalanceJournal->update($validated);

        return redirect()->route('daily-balance-journals.index')->with('success', 'Мэдээ амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DailyBalanceJournal  $dailyBalanceJournal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(DailyBalanceJournal $dailyBalanceJournal)
    {
        $dailyBalanceJournal->delete();
        return redirect()->route('daily-balance-journals.index')->with('success', 'Мэдээ устгагдлаа.');
    }

    // public function dailyMatrixReport(Request $request)
    // {
    //     // Сарын шүүлтүүр авах (YYYY-MM хэлбэрээр)
    //     $selectedMonth = $request->input('month', now()->format('Y-m'));
    //     $startOfMonth = Carbon::parse($selectedMonth . '-01');
    //     $endOfMonth = $startOfMonth->copy()->endOfMonth();
    //     $daysInMonth = $startOfMonth->daysInMonth;

    //     // Журналын өгөгдлийг тухайн сараар шүүнэ
    //     $journals = DailyBalanceJournal::with('powerPlant')
    //         ->selectRaw('
    //         power_plant_id,
    //         date,
    //         SUM(processed_amount) as processed,
    //         SUM(distribution_amount) as distributed,
    //         SUM(internal_demand) as internal_demand,
    //         AVG(percent) as percent
    //     ')
    //         ->whereBetween('date', [$startOfMonth, $endOfMonth])
    //         ->groupBy('power_plant_id', 'date')
    //         ->orderBy('date')
    //         ->get();

    //     $plants = [];
    //     $pivot = [];

    //     // Журнал өгөгдлөөс pivot үүсгэх
    //     foreach ($journals as $row) {
    //         $plant = $row->powerPlant->name ?? 'Unknown';
    //         $day = Carbon::parse($row->date)->day;

    //         // Станцын нэр хадгалах
    //         $plants[$plant] = true;

    //         // 🌟 plant_id-г заавал хадгална
    //         $pivot[$plant]['plant_id'] = $row->power_plant_id;

    //         $pivot[$plant]['processed'][$day] = $row->processed;
    //         $pivot[$plant]['distributed'][$day] = $row->distributed;
    //         $pivot[$plant]['internal_demand'][$day] = $row->internal_demand;
    //         $pivot[$plant]['percent'][$day] = $row->percent;
    //     }


    //     // Бүх станцад тухайн сарын бүх өдөрт default 0 өгөгдөл үүсгэх
    //     foreach ($plants as $plant => $_) {
    //         foreach (range(1, $daysInMonth) as $day) {
    //             foreach (['processed', 'distributed', 'internal_demand', 'percent'] as $key) {
    //                 if (!isset($pivot[$plant][$key][$day])) {
    //                     $pivot[$plant][$key][$day] = 0;
    //                 }
    //             }
    //         }

    //         // Өдөр бүрийг сортлох, plant_id-г оролцуулахгүй
    //         foreach ($pivot[$plant] as $key => &$values) {
    //             if (is_array($values)) {
    //                 ksort($values);
    //             }
    //         }
    //     }


    //     ksort($plants);

    //     return view('daily_balance_journals.report', [
    //         'pivot' => $pivot,
    //         'plants' => array_keys($plants),
    //         'days' => range(1, $daysInMonth),
    //         'selectedMonth' => $selectedMonth,
    //     ]);
    // }
    public function dailyMatrixReport(Request $request)
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01');
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // 1. Журналын өгөгдөл
        $journals = DailyBalanceJournal::with('powerPlant')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        // 2. Battery өгөгдөл
        $batteries = DailyBalanceBattery::with('powerPlant')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        // 3. Импорт/Экспорт өгөгдөл
        $importExports = DailyBalanceImportExport::with('powerPlant')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $pivot = [];
        $plants = [];

        /* -----------------------------
       1. DailyBalanceJournal data
    ------------------------------*/
        foreach ($journals as $row) {
            $plant = $row->powerPlant->name;
            $day = Carbon::parse($row->date)->day;

            $plants[$plant] = true;
            $pivot[$plant]['plant_id'] = $row->power_plant_id;

            // ЭНЭ СТАНЦАД JOURNAL ӨГӨГДӨЛ БАЙГААГ ТЭМДЭГЛЭХ
            $pivot[$plant]['has_journal'] = true;

            $pivot[$plant]['processed'][$day] = $row->processed_amount;
            $pivot[$plant]['distributed'][$day] = $row->distribution_amount;
            $pivot[$plant]['internal_demand'][$day] = $row->internal_demand;
            $pivot[$plant]['percent'][$day] = $row->percent;
        }

        /* -----------------------------
       2. Battery data
    ------------------------------*/
        foreach ($batteries as $row) {
            $plant = $row->powerPlant->name;
            $day = Carbon::parse($row->date)->day;

            $plants[$plant] = true;
            $pivot[$plant]['plant_id'] = $row->power_plant_id;

            // ЭНЭ СТАНЦАД BATTERY ӨГӨГДӨЛ БАЙГААГ ТЭМДЭГЛЭХ
            $pivot[$plant]['has_battery'] = true;

            $pivot[$plant]['battery_given'][$day] = $row->energy_given;
            $pivot[$plant]['battery_taken'][$day] = $row->energy_taken;
        }

        /* -----------------------------
       3. Import / Export data
    ------------------------------*/
        foreach ($importExports as $row) {
            $plant = $row->powerPlant->name;
            $day = Carbon::parse($row->date)->day;

            $plants[$plant] = true;
            $pivot[$plant]['plant_id'] = $row->power_plant_id;

            // ЭНЭ СТАНЦАД IMPORT/EXPORT ӨГӨГДӨЛ БАЙГААГ ТЭМДЭГЛЭХ
            $pivot[$plant]['has_import_export'] = true;

            $pivot[$plant]['import'][$day] = $row->import;
            $pivot[$plant]['export'][$day] = $row->export;
        }

        /* -----------------------------
       Өдөр бүр default 0 үүсгэх
    ------------------------------*/
        foreach ($plants as $plant => $_) {
            foreach (range(1, $daysInMonth) as $day) {
                $keys = [
                    'processed',
                    'distributed',
                    'internal_demand',
                    'percent',
                    'battery_given',
                    'battery_taken',
                    'import',
                    'export'
                ];

                foreach ($keys as $key) {
                    if (!isset($pivot[$plant][$key][$day])) {
                        $pivot[$plant][$key][$day] = 0;
                    }
                }
            }

            // Өдөр сортлох
            foreach ($pivot[$plant] as $key => &$values) {
                if (is_array($values)) {
                    ksort($values);
                }
            }
        }

        ksort($plants);

        return view('daily_balance_journals.report', [
            'pivot' => $pivot,
            'plants' => array_keys($plants),
            'days' => range(1, $daysInMonth),
            'selectedMonth' => $selectedMonth,
        ]);
    }


    public function showPlant(Request $request, $plantId)
    {
        // Сар сонгох, default = өнөөдрийн сар
        $month = $request->input('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        // Сонгосон станцын мэдээллийг аваад сар, өдөрөөр эрэмбэлэх
        $journals = DailyBalanceJournal::with('powerPlant')
            ->where('power_plant_id', $plantId)
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->orderBy('date', 'asc')
            ->get();

        return view('daily_balance_journals.plant_show', [
            'journals' => $journals,
            'month' => $month,
            'selectedPlant' => $plantId
        ]);
    }
}
