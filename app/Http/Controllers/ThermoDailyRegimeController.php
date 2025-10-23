<?php

namespace App\Http\Controllers;

use App\Models\ThermoDailyRegime;
use App\Models\PowerPlant;
use Illuminate\Http\Request;

class ThermoDailyRegimeController extends Controller
{
    public function index()
    {
        $regimes = ThermoDailyRegime::with('powerPlant')->orderByDesc('date')->paginate(10);
        return view('thermo_daily_regimes.index', compact('regimes'));
    }

    public function create()
    {
        $powerPlants = PowerPlant::whereIn('id', [1, 9, 11, 19, 20, 21, 22])->orderBy('Order')->get();
        return view('thermo_daily_regimes.create', compact('powerPlants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'time_range' => 'required|in:0-8,8-16,16-24',
            'temperature' => 'nullable|numeric',
            't1' => 'nullable|numeric',
            't2' => 'nullable|numeric',
            'p1' => 'nullable|numeric',
            'p2' => 'nullable|numeric',
            'd' => 'nullable|numeric',
            'g' => 'nullable|numeric',
            'q' => 'nullable|numeric',
            'q_total' => 'nullable|numeric',
        ]);

        ThermoDailyRegime::create($validated);
        return redirect()->route('thermo-daily-regimes.index')->with('success', 'Амжилттай нэмэгдлээ.');
    }

    public function edit(ThermoDailyRegime $thermoDailyRegime)
    {
        $powerPlants = PowerPlant::whereIn('id', [1, 9, 11, 19, 20, 21, 22])->orderBy('Order')->get();
        return view('thermo_daily_regimes.edit', compact('thermoDailyRegime', 'powerPlants'));
    }

    public function update(Request $request, ThermoDailyRegime $thermoDailyRegime)
    {
        $validated = $request->validate([
            'power_plant_id' => 'required|exists:power_plants,id',
            'date' => 'required|date',
            'time_range' => 'required|in:0-8,8-16,16-24',
            'temperature' => 'nullable|numeric',
            't1' => 'nullable|numeric',
            't2' => 'nullable|numeric',
            'p1' => 'nullable|numeric',
            'p2' => 'nullable|numeric',
            'd' => 'nullable|numeric',
            'g' => 'nullable|numeric',
            'q' => 'nullable|numeric',
            'q_total' => 'nullable|numeric',
        ]);

        $thermoDailyRegime->update($validated);
        return redirect()->route('thermo-daily-regimes.index')->with('success', 'Амжилттай засагдлаа.');
    }

    public function destroy(ThermoDailyRegime $thermoDailyRegime)
    {
        $thermoDailyRegime->delete();
        return redirect()->route('thermo-daily-regimes.index')->with('success', 'Амжилттай устгагдлаа.');
    }

    public function report(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $powerPlants = PowerPlant::whereIn('id', [1, 9, 11, 19, 20, 21, 22])->orderBy('Order')->get();

        $regimes = ThermoDailyRegime::whereDate('date', $date)->get();

        return view('thermo_daily_regimes.report', compact('powerPlants', 'regimes', 'date'));
    }
}
