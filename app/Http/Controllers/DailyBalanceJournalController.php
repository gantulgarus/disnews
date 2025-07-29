<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\DailyBalanceJournal;

class DailyBalanceJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $journals = DailyBalanceJournal::with('powerPlant')->latest()->paginate(10);
        return view('daily_balance_journals.index', compact('journals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $powerPlants = PowerPlant::all();
        return view('daily_balance_journals.create', compact('powerPlants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'entry_date_time' => 'required|date',
            'time_range' => 'required|string|max:255',
            'processed_amount' => 'nullable|numeric',
            'distribution_amount' => 'nullable|numeric',
            'internal_demand' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
            'positive_deviation' => 'nullable|numeric',
            'negative_deviation_spot' => 'nullable|numeric',
            'negative_deviation_import' => 'nullable|numeric',
            'positive_resolution' => 'nullable|numeric',
            'negative_resolution' => 'nullable|numeric',
            'deviation_reason' => 'nullable|string',
            'by_consumption_growth' => 'nullable|numeric',
            'by_other_station_issue' => 'nullable|numeric',
            'dispatcher_name' => 'required|string|max:255',
        ]);

        DailyBalanceJournal::create($validated);

        return redirect()->route('daily-balance-journals.index')->with('success', 'Журнал амжилттай үүслээ.');
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
        $powerPlants = PowerPlant::all();
        return view('daily_balance_journals.edit', compact('dailyBalanceJournal', 'powerPlants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyBalanceJournal $dailyBalanceJournal)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'entry_date_time' => 'required|date',
            'time_range' => 'required|string|max:255',
            'processed_amount' => 'nullable|numeric',
            'distribution_amount' => 'nullable|numeric',
            'internal_demand' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
            'positive_deviation' => 'nullable|numeric',
            'negative_deviation_spot' => 'nullable|numeric',
            'negative_deviation_import' => 'nullable|numeric',
            'positive_resolution' => 'nullable|numeric',
            'negative_resolution' => 'nullable|numeric',
            'deviation_reason' => 'nullable|string',
            'by_consumption_growth' => 'nullable|numeric',
            'by_other_station_issue' => 'nullable|numeric',
            'dispatcher_name' => 'required|string|max:255',
        ]);

        $dailyBalanceJournal->update($validated);

        return redirect()->route('daily-balance-journals.index')->with('success', 'Журнал амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyBalanceJournal $dailyBalanceJournal)
    {
        $dailyBalanceJournal->delete();
        return redirect()->route('daily-balance-journals.index')->with('success', 'Журнал устгагдлаа.');
    }

    public function dailyMatrixReport(Request $request)
    {
        // Сарын шүүлтүүр авах (YYYY-MM хэлбэрээр)
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01');
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // Журналын өгөгдлийг тухайн сараар шүүнэ
        $journals = DailyBalanceJournal::with('powerPlant')
            ->selectRaw('
            power_plant_id,
            DATE(entry_date_time) as date,
            SUM(processed_amount) as processed,
            SUM(distribution_amount) as distributed,
            SUM(internal_demand) as internal_demand,
            SUM(percent) as percent
        ')
            ->whereBetween('entry_date_time', [$startOfMonth, $endOfMonth])
            ->groupBy('power_plant_id', 'date')
            ->orderBy('date')
            ->get();

        $plants = [];
        $pivot = [];

        // Журнал өгөгдлөөс pivot үүсгэх
        foreach ($journals as $row) {
            $plant = $row->powerPlant->name ?? 'Unknown';
            $day = Carbon::parse($row->date)->day;

            $plants[$plant] = true;

            $pivot[$plant]['processed'][$day] = $row->processed;
            $pivot[$plant]['distributed'][$day] = $row->distributed;
            $pivot[$plant]['internal_demand'][$day] = $row->internal_demand;
            $pivot[$plant]['percent'][$day] = $row->percent;
        }

        // Бүх станцад тухайн сарын бүх өдөрт default 0 өгөгдөл үүсгэх
        foreach ($plants as $plant => $_) {
            foreach (range(1, $daysInMonth) as $day) {
                foreach (['processed', 'distributed', 'internal_demand', 'percent'] as $key) {
                    if (!isset($pivot[$plant][$key][$day])) {
                        $pivot[$plant][$key][$day] = 0;
                    }
                }
            }
            // Өдрийг сортлох
            foreach ($pivot[$plant] as &$values) {
                ksort($values);
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
}
