<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use Illuminate\Http\Request;
use App\Models\ThermoDailyRegime;
use Illuminate\Support\Facades\Auth;

class ThermoDailyRegimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userOrgId = $user->organization_id;

        // Query эхлүүлэх
        $query = ThermoDailyRegime::query()->orderBy('date', 'asc');

        if ($userOrgId != 5) {
            $query->whereHas('powerPlant', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId);
            });
        }

        // 📌 Сарын фильтр
        if ($request->filled('month')) {
            // month = 2025-02 гэх мэт
            $query->whereYear('date', substr($request->month, 0, 4))
                ->whereMonth('date', substr($request->month, 5, 2));
        }


        $regimes = $query->get();

        return view('thermo_daily_regimes.index', compact('regimes'));
    }

    public function create()
    {
        $user = Auth::user();
        $userOrgId = $user->organization_id;

        if ($userOrgId == 5) {
            // Админ -> бүх станц
            $powerPlants = PowerPlant::whereIn('id', [1, 9, 11, 19, 20, 21, 22])->orderBy('Order')->get();
        } else {
            // Админ биш -> зөвхөн өөрийн байгууллагын станцууд
            $powerPlants = PowerPlant::where('organization_id', $userOrgId)->get();
        }


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
        $user = Auth::user();
        $userOrgId = $user->organization_id;

        if ($userOrgId == 5) {
            // Админ -> бүх станц
            $powerPlants = PowerPlant::whereIn('id', [1, 9, 11, 19, 20, 21, 22])->orderBy('Order')->get();
        } else {
            // Админ биш -> зөвхөн өөрийн байгууллагын станцууд
            $powerPlants = PowerPlant::where('organization_id', $userOrgId)->get();
        }

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
